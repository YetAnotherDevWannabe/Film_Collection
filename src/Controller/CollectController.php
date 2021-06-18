<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Entity\Film;
use App\Form\CollectFormType;
use App\Form\FilmFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/collect", name="collect_")
 */
class CollectController extends AbstractController
{

	/**
	 * Page controller to create a new collection
	 * @Route("/nouvelle-collection/", name="new")
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

			// Redirect the user to the 'add_film' page
			return $this->redirectToRoute('main_home');
		}

		return $this->render('collect/New.html.twig',
			[
				'collectForm' => $collectForm->createView(),
			]);
	}

	/**
	 * Page controller to add a film to collect
	 * @Route("/ajouter-film/", name="add_film")
	 * @Security ("is_granted('ROLE_ADMIN')")
	 */
	public function filmAdd(Request $request): Response
	{
		// Create a new instance of film
		$newFilm = new Film();
		$filmForm = $this->createForm(FilmFormType::class, $newFilm);
		$filmForm->handleRequest($request);

		// If no error and form submitted
		if ( $filmForm->isSubmitted() && $filmForm->isValid() )
		{
			// Deactivate account in DB but keep it in the DB
			$newFilm
				// ->setTmdbId($filmForm->get('tmdbId')->getData())
				// ->setName($filmForm->get('name')->getData())
				// ->setYear($filmForm->get('year')->getData())
				// ->setGenre($filmForm->get('genre')->getData())
				// ->setSynopsis($filmForm->get('synopsis')->getData())
				// ->setPoster($filmForm->get('poster')->getData())
				// ->setNote($filmForm->get('note')->getData())
				// ->setTrailer($filmForm->get('trailer')->getData())
				->setUser($this->getUser())
			;

			// Save the new film data in the DB via the entity manager
			$em = $this->getDoctrine()->getManager();
			$em->persist($newFilm);
			$em->flush();

			if ( $filmForm->getClickedButton() === $filmForm->get('addNext') )
			{
				// TODO: redirect to added film page
				// TODO: ajouter un lien pour rajouter un autre film dans le message flash
				$this->addFlash('success', 'Le film a bien été ajouté.');
				return $this->redirectToRoute('main_home');
			}
		}

		return $this->render('collect/AddFilm.html.twig',
			[
				'filmForm' => $filmForm->createView(),
			]);
	}

	/**
	 * Controller for the collect list page
	 * @Route("/collection-list/", name="list")
	 */
	public function listCollect(Request $request, PaginatorInterface $paginator): Response
	{
		$requestedPage = $request->query->getInt('page', 1);
		// If requested page < 1 error 404
		if ( $requestedPage < 1 ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		// Create a query for paginator to get only the collections shown on the current page
		$query = $em->createQuery('SELECT c FROM App\Entity\Collect c ORDER BY c.publicationDate DESC');

		// Get the number of collection to show on each page from services.yaml
		$collectNumberByPage = $this->getParameter('collection_number_by_page');

		// On stock dans $articles le nombre d' articles de la page demandé dans l' URL
		$collects = $paginator->paginate($query, $requestedPage, $collectNumberByPage);

		return $this->render('collect/List.html.twig',
		[
			'collects' => $collects,
		]);
	}
}
