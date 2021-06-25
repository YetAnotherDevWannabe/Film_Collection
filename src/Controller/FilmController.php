<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Entity\Film;
use App\Form\FilmFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/film", name="film_")
 */
class FilmController extends AbstractController
{
	/**
	 * @Route("/ajouter/", name="add")
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
			$newFilm->setUser($this->getUser());

			// Save the new film in the DB
			$em = $this->getDoctrine()->getManager();
			$em->persist($newFilm);
			$em->flush();

			// TODO: redirect to added film page correctly
			$this->addFlash('success', 'Le film a bien été ajouté.</br><a class="text-basecolor fs-6" href="/film/ajouter/"><i class="fas fa-plus me-1"></i>Ajouter un autre film</a>');
			return $this->redirectToRoute('film_list');
		}

		return $this->render('film/add.html.twig', ['filmForm' => $filmForm->createView(),]);
	}

	/**
	 * @Route("/liste/", name="list")
	 */
	public function filmList(Request $request, PaginatorInterface $paginator): Response
	{
		$requestedPage = $request->query->getInt('page', 1);
		// If requested page < 1 error 404
		if ( $requestedPage < 1 ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery('SELECT f FROM App\Entity\Film f ORDER BY f.id ASC');

		// Get the number of collection to show on each page from services.yaml
		$filmNumberByPage = $this->getParameter('entity_number_by_page');

		// On stock dans $articles le nombre d' articles de la page demandé dans l' URL
		$films = $paginator->paginate($query, $requestedPage, $filmNumberByPage);
		if ( ceil(($films->getTotalItemCount() / $filmNumberByPage)) < $requestedPage ) throw new NotFoundHttpException();

		return $this->render('film/list.html.twig', ['films' => $films,]);
	}

	/**
	 * @Route("/detail/{slug}/", name="detail")
	 */
	public function filmDetail(Film $film, Request $request): Response
	{
		if ( $this->getUser() )
		{
			$userCollects = $this->getUser()->getCollects();
		}
		else
		{
			$userCollects = null;
		}

		// Used to get the last page user was on
		$lastPage = $request->request->get('referer');

		return $this->render('film/detail.html.twig',
			[
				'film'         => $film,
				'userCollects' => $userCollects,
				'lastPage'     => $lastPage,
			]);
	}

	/**
	 * @Route("/supression/{id}/", name="delete")
	 * @Security ("is_granted('ROLE_ADMIN')")
	 */
	public function filmDelete(Film $film, Request $request): Response
	{
		// Get CSRF token from URL
		$tokenCSRF = $request->query->get('csrf_token');

		// Check CSRF token
		if ( !$this->isCsrfTokenValid('film_delete' . $film->getId(), $tokenCSRF) )
		{
			$this->addFlash('error', 'Token CSRF invalide.');
		}
		else
		{
			$em = $this->getDoctrine()->getManager();
			$em->remove($film);
			$em->flush();

			$this->addFlash('success', 'Film supprimé !');
		}

		return $this->redirectToRoute('admin_film_delete');
	}

	/**
	 * @Route("/recherche/", name="search")
	 */
	public function filmSearch(Request $request, PaginatorInterface $paginator): Response
	{
		$requestedPage = $request->query->getInt('page', 1);
		// Si le numéro de page demandé dans l'URL est inférieur à 1, erreur 404
		if ( $requestedPage < 1 ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		// Récupération de la recherche dans le formulaire
		$search = $request->query->get('q');

		// Création de la requête
		$query = $em
			->createQuery('SELECT a FROM App\Entity\Film a WHERE a.name LIKE :search ORDER BY a.year DESC')
			->setParameters(['search' => '%' . $search . '%']);

		// Get the number of collection to show on each page from services.yaml
		$filmNumberByPage = $this->getParameter('entity_number_by_page');

		// Récupération des articles
		$films = $paginator->paginate($query, $requestedPage, $filmNumberByPage);
		if ( !empty($films->getItems()) && ceil(( $films->getTotalItemCount() / $filmNumberByPage )) < $requestedPage ) throw new NotFoundHttpException();

		$em = $this->getDoctrine()->getManager();
		$filmRepo = $em->getRepository(Film::class);
		// $filmsSearched = $filmRepo->findBy();

		return $this->render('film/search.html.twig', ['films' => $films, 'search' => $search]);
	}
}
