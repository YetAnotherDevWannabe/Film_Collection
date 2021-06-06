<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
	/**
	 * @Route("/", name="main_home")
	 */
	public function index(): Response
	{
		$this->addFlash('success', 'success');
		$this->addFlash('info', 'info');
		$this->addFlash('warning', 'warning');
		$this->addFlash('error', 'error');
		return $this->render('main/index.html.twig');
	}

	/**
	 * @Route("/profil/", name="main_profil")
	 */
	public function login(): Response
	{
		// return $this->render('main/index.html.twig');
	}

	/**
	 * @Route("/profil/edit/photo/", name="main_edit_avatar")
	 */
	public function editAvatar(): Response
	{
		// return $this->render('main/index.html.twig');
	}
}
