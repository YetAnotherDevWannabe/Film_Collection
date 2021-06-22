<?php

namespace App\Controller;

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
	 * @Route("/detail/{slug}/", name="view")
	 */
	public function filmView(Film $film): Response
	{
		return $this->render('film/view.html.twig', ['film' => $film,]);
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
