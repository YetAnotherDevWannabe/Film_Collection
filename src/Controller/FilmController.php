<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Entity\Film;
use App\Form\FilmFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
	public function filmList(): Response
	{
		$em = $this->getDoctrine()->getManager();
		$filmRepo = $em->getRepository(Film::class);
		$films = $filmRepo->findAll();

		return $this->render('film/list.html.twig', ['films' => $films]);
	}

	/**
	 * @Route("/detail/{slug}/", name="detail")
	 */
	public function filmDetail(Film $film, Request $request): Response
	{
		// Used to get the last page user was on
		$lastPage = $request->request->get('referer');
		$userCollects = $this->getUser()->getCollects();

		return $this->render('film/detail.html.twig',
			[
				'film'          => $film,
				'userCollects'  => $userCollects,
				'lastPage'      => $lastPage,
			]);
	}

	/**
	 * @Route("/supression/{id}/", name="delete")
	 * @Security ("is_granted('ROLE_ADMIN')")
	 */
	public function filmDelete(Film $film, Request $request): Response
	{
		// Récupération du token CSRF dans l' URL
		$tokenCSRF = $request->query->get('csrf_token');

		// Vérification de la validité du token CSRF
		if ( !$this->isCsrfTokenValid('film_delete' . $film->getId(), $tokenCSRF) )
		{
			$this->addFlash('error', 'Token CSRF invalide.');
		}
		else
		{
			// Suppression de l' article
			$em = $this->getDoctrine()->getManager();
			$em->remove($film);
			$em->flush();

			// Message flash
			$this->addFlash('success', 'Film supprimé !');
		}

		return $this->redirectToRoute('admin_film_delete');
	}

	/**
	 * @Route("/recherche/", name="search")
	 */
	public function filmSearch(): Response
	{
		$em = $this->getDoctrine()->getManager();
		$filmRepo = $em->getRepository(Film::class);
		// $filmsSearched = $filmRepo->findBy();

		return $this->render('film/search.html.twig', []);
	}
}
