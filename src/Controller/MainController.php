<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AvatarEditFormType;
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
	 * @Route("/profil/edit/avatar/", name="main_avatar_edit")
	 * 
	 */
	public function editAvatar(Request $request): Response
	{

		$form = $this->createForm(AvatarEditFormType::class);

		$form->handleRequest($request);


		if($form->isSubmitted() && $form->isValid())
		{



			$avatar = $form->get('avatar')->getData();

			$profilAvatarDir = $this->getParameter('users_uploaded_avatar_dir');

			$connectedUser = $this->getUser();

			// TODO : à décommenter quand l'upload à l'inscription fonctionnera
			// if($connectedUser->getAvatar() != null){
			// 	unlink( $profilAvatarDir . $connectedUser->getAvatar() );

			// }

			do{

				$newFileName = md5($connectedUser->getId() . random_bytes(100)) . '.' . $avatar->guessExtension();

			
			} while( file_exists( $profilAvatarDir . $newFileName) );

			$connectedUser->setAvatar($newFileName);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$avatar->move(
				$profilAvatarDir,
				$newFileName
			);

			$this->addFlash('succès', 'Votre avatar a été modifié !');

			return $this->redirectToRoute('main_profil');

		}

		return $this->render('main/avatar.edit.html.twig', [
			'avatarForm' => $form->createView(),
		]);

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
