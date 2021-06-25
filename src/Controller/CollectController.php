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
	 * Page controller to create a new collection
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
			$this->addFlash('success', 'Votre collection a bien été créé.');
			return $this->redirectToRoute('collect_list');
		}

		return $this->render('collect/add.html.twig', ['collectForm' => $collectForm->createView(),]);
	}

	/**
	 * Controller for the collect list page
	 * @Route("/liste/", name="list")
	 */
	public function listCollect(Request $request, PaginatorInterface $paginator): Response
	{
		$requestedPage = $request->query->getInt('page', 1);
		// If requested page < 1 error 404
		if ( $requestedPage < 1 /*|| $requestedPage > 1*/ ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		// Create a query for paginator to get only the collections shown on the current page
		$query = $em->createQuery('SELECT c FROM App\Entity\Collect c ORDER BY c.publicationDate DESC');

		// Get the number of collection to show on each page from services.yaml
		$collectNumberByPage = $this->getParameter('entity_number_by_page');

		// On stock dans $articles le nombre d' articles de la page demandé dans l' URL
		$collects = $paginator->paginate($query, $requestedPage, $collectNumberByPage);

		return $this->render('collect/list.html.twig', ['collects' => $collects,]);
	}

	/**
	 * Controller for the collect view page
	 * @Route("/detail/{slug}/", name="detail")
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
	 * Controller for the commentCollect deletion
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

		else if( $userRole == 'ROLE_ADMIN' || $user->getId() == $commentCollect->getUser()->getId() ){

            // disable the comment (deletion for user)
            $commentCollect
                ->setActive(false);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Votre commentaire à bien été supprimer !');

        }

        else
		{
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à faire cette action.');
		}

		return $this->redirectToRoute('collect_detail', [
			'slug' => $commentCollect->getCollect()->getSlug(),
		]);
	}

	/**
	 * Controller for the collect deletion
	 * @Route("/supprimer/{id}", name="delete")
	 */
	public function deleteCollect(Collect $collect, Request $request): Response
	{
		// Redirects to 'collect_list' if user is not either ADMIN or the AUTHOR
		$user = $this->getUser();
		if ( $user->getRoles()[0] != 'ROLE_ADMIN' || ($user && $user->getId() != $collect->getAuthor()->getId()) ) //TODO: Suppr impossible if user author
		{
			return $this->redirectToRoute('collect_list');
		}

		// Creates form for display
		$collectDeleteForm = $this->createForm(CollectDeleteFormType::class);
		$collectDeleteForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $collectDeleteForm->getClickedButton() === $collectDeleteForm->get('cancel') )
		{
			return $this->redirectToRoute('collect_list');
		}

		if ( $collectDeleteForm->isSubmitted() && $collectDeleteForm->isValid() )
		{

			// Remove collect from DB
			$em = $this->getDoctrine()->getManager();
			$em->remove($collect);
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Collection supprimé.');
			return $this->redirectToRoute('collect_list');
		}


		return $this->render('collect/delete.html.twig',
			[
				'collectDeleteForm' => $collectDeleteForm->createView(),
				'collect'           => $collect,
			]);
	}

	/**
	 * Controller for the search page
	 * @Route("/{collect_id}/ajouter/film/{id}/", name="film_add")
	 * @Entity("collect", expr="repository.find(collect_id)")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function filmAdd(Collect $collect, Film $film, Request $request): Response //Collect $collect, Film $film,
	{
		if ( $this->getUser() != $collect->getAuthor() || !$this->getUser() )
		{
			return $this->redirectToRoute('film_detail', ['slug' => $film->getSlug()]);
		}

		$collect->addFilmCollect($film);

		$em = $this->getDoctrine()->getManager();
		$em->flush($collect);

		// Message flash
		$this->addFlash('success', '<span class="text-compliment">' . $film->getName() . '</span> a ajouté à la collection <span class="text-compliment">' . $collect->getName() . '</span> !');

		return $this->redirectToRoute('film_detail', ['slug' => $film->getSlug()]);
		// return $this->redirectToRoute('film_detail', array('slug' => $film->getSlug()));
	}
}
