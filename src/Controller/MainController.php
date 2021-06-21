<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\DeleteAvatarFormType;
use App\Form\EditAvatarFormType;
use App\Form\ProfilDeleteFormType;
use App\Form\ProfilEditFormType;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
	/**
	 * Controller for the home page
	 * @Route("/", name="main_home")
	 */
	public function home(): Response
	{
		// $this->addFlash('error', 'error');
		// $this->addFlash('warning', 'warning');
		// $this->addFlash('info', 'info');
		// $this->addFlash('success', 'success');

		return $this->render('main/index.html.twig');
	}


	/**
	 * Controller for the profil page
	 * @Route("/profil/", name="main_profil")
	 */
	public function profil(): Response
	{
		return $this->render('main/profil.html.twig');
	}


	/**
	 * Controller for the profil edition page
	 * @Route("/profil/edit/", name="main_profil_edit")
	 */
	public function profilEdit(): Response
	{
		return $this->render('main/profil-edit.html.twig');
	}


	/**
	 * Controller for the profil suppression page
	 * @Route("/profil/delete/", name="main_profil_delete")
	 */
	public function profilDelete(): Response
	{
		return $this->render('main/profil-delete.html.twig');
	}


	/**
	 * Controller for the avatar edition page
	 * @Route("/profil/avatar/edit/", name="main_avatar_edit")
	 */
	public function avatarEdit(): Response
	{
		return $this->render('main/profil-edit-avatar.html.twig');
	}


	/**
	 * Controller for the avatar suppression page
	 * @Route("/profil/avatar/delete/", name="main_avatar_delete")
	 */
	public function avatarDelete(): Response
	{
		return $this->render('main/profil-delete-avatar.html.twig');
	}
}
