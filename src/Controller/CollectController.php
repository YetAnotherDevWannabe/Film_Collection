<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Entity\CommentCollect;
use App\Entity\Film;
use App\Form\CollectDeleteFormType;
use App\Form\CollectFormType;
use App\Form\CommentCollectFormType;
use App\Form\FilmFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collection", name="collect_")
 */
class CollectController extends AbstractController
{
	/**
	 * @Route("/ajouter/", name="new")
	 * @Security ("is_granted('ROLE_USER')")
	 */
	public function createCollect(Request $request): Response
	{

		$newCollect = new Collect();
		$collectForm = $this->createForm(CollectFormType::class, $newCollect);
		$collectForm->handleRequest($request);

		// If no error and form submitted
		if ( $collectForm->isSubmitted() && $collectForm->isValid() )
		{

			// Hydrate the user with the missing data (not coming from the form)
			$newCollect
				->setPublicationDate(new \DateTime())
				->setAuthor($this->getUser());

			// Save the new collect in the DB via the entity manager
			$em = $this->getDoctrine()->getManager();
			$em->persist($newCollect);
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Votre collection a bien été créée.');
			return $this->redirectToRoute('collect_list');
		}

		return $this->render('collect/add.html.twig', ['collectForm' => $collectForm->createView(),]);
	}


	/**
	 * @Route("/liste/", name="list")
	 */
	public function listCollect(Request $request, PaginatorInterface $paginator): Response
	{
		$search = false;
		$requestedPage = $request->query->getInt('page', 1);
		// If requested page < 1 error 404
		if ( $requestedPage < 1 ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		// Create a query for paginator to get only the collections shown on the current page
		$query = $em->createQuery('SELECT c FROM App\Entity\Collect c ORDER BY c.publicationDate DESC');

		// Get the number of collection to show on each page from services.yaml
		$collectNumberByPage = $this->getParameter('entity_number_by_page');

		// On stock dans $articles le nombre d' articles de la page demandé dans l' URL
		$collects = $paginator->paginate($query, $requestedPage, $collectNumberByPage);
		 if ( ceil(( $collects->getTotalItemCount() / $collectNumberByPage )) < $requestedPage && $collects->getTotalItemCount() != 0 ) throw new NotFoundHttpException();

		return $this->render('collect/list.html.twig', ['collects' => $collects, 'search' => $search]);
	}


	/**
	 * @Route("/{slug}/", name="detail")
	 */
	public function detailCollect(Request $request, Collect $collect): Response
	{
		//if user is not connected we call directly the view without processing the form below
		if ( !$this->getUser() )
		{

			return $this->render('collect/detail.html.twig',
				[
					'collect' => $collect,
				]);
		}

		$comment = new CommentCollect();
		$commentForm = $this->createForm(CommentCollectFormType::class, $comment);
		$commentForm->handleRequest($request);

		//Verification that the form has been sent and has no errors
		if ( $commentForm->isSubmitted() && $commentForm->isValid() )
		{

			$comment
				->setPublicationDate(new \DateTime())
				->setCollect($collect)
				->setUser($this->getUser())
				->setActive(true);

			$em = $this->getDoctrine()->getManager();
			$em->persist($comment);
			$em->flush();

			$this->addFlash('success', 'Commentaire publié avec succès !');

			return $this->redirectToRoute('collect_detail', [
				'slug' => $collect->getSlug(),
			]);

		}

		return $this->render('collect/detail.html.twig',
			[
				'collect'     => $collect,
				'commentForm' => $commentForm->createView(),
			]);
	}


	/**
	 * @Route("/commentaire/suppression/{id}", name="comment_delete")
	 * @Security ("is_granted('ROLE_USER')")
	 */
	public function deleteCommentCollect(CommentCollect $commentCollect, Request $request): Response
	{
		$user = $this->getUser();
		$userRole = $this->getUser()->getRoles()[0];
		$tokenCSRF = $request->query->get('csrf_token');

		// Verify if token is valid
		if ( !$this->isCsrfTokenValid('collect_comment_delete' . $commentCollect->getId(), $tokenCSRF) )
		{
			$this->addFlash('error', 'Token de sécurité invalide, veuillez ré-essayer.');
		}
		else if ( $userRole == 'ROLE_ADMIN' || $user->getId() == $commentCollect->getUser()->getId() )
		{

			// disable the comment
			$commentCollect->setActive(false);

			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$this->addFlash('success', 'Votre commentaire à bien été supprimé !');
		}
		else
		{
			$this->addFlash('error', 'Vous n\'êtes pas autorisé à faire cette action.');
		}

		return $this->redirectToRoute('collect_detail', ['slug' => $commentCollect->getCollect()->getSlug(),]);
	}


	/**
	 * @Route("/supprimer/{id}", name="delete")
	 * @Security ("is_granted('ROLE_USER')")
	 */
	public function deleteCollect(Collect $collect, Request $request): Response
	{
		$collectDeleteForm = $this->createForm(CollectDeleteFormType::class);
		$collectDeleteForm->handleRequest($request);

		$user = $this->getUser();
		$userRole = $this->getUser()->getRoles()[0];
		$tokenCSRF = $request->query->get('csrf_token');

		if ( $userRole == 'ROLE_ADMIN' || $user->getId() == $collect->getAuthor()->getId() )
		{
			// If Cancel button is clicked
			if ( $collectDeleteForm->getClickedButton() === $collectDeleteForm->get('cancel') )
			{
				return $this->redirectToRoute('collect_list');
			}

			if ( $collectDeleteForm->isSubmitted() && $collectDeleteForm->isValid() )
			{
				// disable the comment
				$em = $this->getDoctrine()->getManager();
				$em->remove($collect);
				$em->flush();

				$this->addFlash('success', 'Collection supprimée.');
				return $this->redirectToRoute('collect_list');
			}
		}
		else
		{
			$this->addFlash('error', 'Vous n\'êtes pas autorisé à faire cette action.');
		}

		return $this->render('collect/delete.html.twig',
			[
				'collectDeleteForm' => $collectDeleteForm->createView(),
				'collect'           => $collect,
			]);
	}


	/**
	 * @Route("/{collect_id}/ajouter/film/{id}/", name="film_add")
	 * @Entity("collect", expr="repository.find(collect_id)")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function filmAdd(Collect $collect, Film $film, Request $request): Response
	{
		if ( $this->getUser() != $collect->getAuthor() || !$this->getUser() )
		{
			return $this->redirectToRoute('film_detail', ['slug' => $film->getSlug()]);
		}

		// Check if film already in the collection
		$alreadyInCollect = false;
		foreach ($collect->getFilmCollect() as $item)
		{
			if ( $item->getId() == $film->getId() )
			{
				$alreadyInCollect = true;
			}
		}
		if ($alreadyInCollect)
		{
			$this->addFlash('info', '<span class="text-complimain font-weight-bold">'. $film->getName() . '</span> fait déjà partie de la collection <span class="text-complimain font-weight-bold">' . $collect->getName() . '</span>.');
			return $this->redirectToRoute('film_detail', ['slug' => $film->getSlug()]);
		}


		$collect->addFilmCollect($film);
		$em = $this->getDoctrine()->getManager();
		$em->flush($collect);

		// Message flash
		$this->addFlash('success', $film->getName() . ' a été ajouté à la collection <a href="/collection/'. $collect->getName() .'" class="text-complimain">' . $collect->getName() . '</a> !');

		return $this->redirectToRoute('film_detail', ['slug' => $film->getSlug()]);
	}


	/**
	 * @Route("/recherche/collection", name="search")
	 */
	public function collectSearch(Request $request, PaginatorInterface $paginator): Response
	{
		// On récupère dans l'URL la donnée GET['page'] (si elle n'existe pas, la valeur par défaut sera "1")
		$requestedPage = $request->query->getInt('page', 1);

		// Si le numéro de page demandé dans l'URL est inférieur à 1, erreur 404
		if ( $requestedPage < 1 )
		{
			throw new NotFoundHttpException();
		}

		// Récupération du manager général des entités
		$em = $this->getDoctrine()->getManager();

		// Récupération de la recherche dans le formulaire
		$search = $request->query->get('q');

		// Création de la requête
		$query = $em
			->createQuery('SELECT a FROM App\Entity\Collect a WHERE a.name LIKE :search ORDER BY a.publicationDate DESC')
			->setParameters(['search' => '%' . $search . '%']);

		// Get the number of collection to show on each page from services.yaml
		$collectNumberByPage = $this->getParameter('entity_number_by_page');

		// Récupération des articles
		$collects = $paginator->paginate(
			$query,
			$requestedPage,
			$collectNumberByPage
		);
		if ( !empty($collects->getItems()) && ceil(( $collects->getTotalItemCount() / $collectNumberByPage )) < $requestedPage ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		$collectRepo = $em->getRepository(Collect::class);


		return $this->render('collect/list.html.twig', ['collects' => $collects, 'search' => $search]);
	}
}
