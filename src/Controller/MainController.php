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
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profil(): Response
	{
		return $this->render('main/profil.html.twig');
	}


	/**
	 * Controller for the profil edition page
	 * @Route("/profil/edit/", name="main_profil_edit")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profilEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
	{
		// TODO: sometimes creates an error when canceling modification (nickname string or null)
		// Create a new instance of user
		$user = $this->getUser();
		$profilEditForm = $this->createForm(ProfilEditFormType::class, $user);
		$profilEditForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $profilEditForm->getClickedButton() === $profilEditForm->get('cancel') )
		{
			return $this->redirectToRoute('main_profil');
		}

		if ( $profilEditForm->isSubmitted() && $profilEditForm->isValid() && $profilEditForm->getClickedButton() === $profilEditForm->get('confirm') )
		{
			// Deactivate account in DB but keep it in the DB
			$user
				->setNickname($profilEditForm->get('nickname')->getData())
				->setEmail($profilEditForm->get('email')->getData())
				->setPassword(
					$passwordEncoder->encodePassword(
						$user,
						$profilEditForm->get('plainPassword')->getData()
					)
				)
				->setActive(true)
			;

			// Save the new user in the DB via the entity manager
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush($user);

			// Success flash message
			$this->addFlash('success', 'Profil mis à jour !');

			// Redirection to profil page
			return $this->redirectToRoute('main_logout');
		}

		return $this->render('main/profil-edit.html.twig',
			[
				'profilEditForm' => $profilEditForm->createView(),
			]);
	}


	/**
	 * Controller for the profil suppression page
	 * @Route("/profil/delete/", name="main_profil_delete")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profilDelete(Request $request): Response
	{
		$profilDeleteForm = $this->createForm(ProfilDeleteFormType::class);
		$profilDeleteForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $profilDeleteForm->getClickedButton() === $profilDeleteForm->get('cancel') )
		{
			return $this->redirectToRoute('main_profil');
		}

		// If Confirm button is clicked
		if ( $profilDeleteForm->isSubmitted() )
		{

			//Get connected user
			$connectedUser = $this->getUser();

			// Deactivate account in DB but keep it in the DB
			$connectedUser->setActive(false);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Compte supprimé');

			// Redirect to 'main_home' page
			return $this->redirectToRoute('main_logout');
		}


		return $this->render('main/profil-delete.html.twig',
			[
				'profilDeleteForm' => $profilDeleteForm->createView(),
			]);
	}


	/**
	 * Controller for the avatar edition page
	 * @Route("/profil/avatar/edit/", name="main_avatar_edit")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function avatarEdit(Request $request): Response
	{
		$editAvatarForm = $this->createForm(EditAvatarFormType::class);
		$editAvatarForm->handleRequest($request);

		// If no error
		if ( $editAvatarForm->isSubmitted() && $editAvatarForm->isValid() )
		{

			// Extract the 'avatar' object sent by the form
			$avatar = $editAvatarForm->get('avatar')->getData();
			// Get location of avatar directory
			$avatarDir = $this->getParameter('users_uploaded_avatar_dir');
			//Get connected user
			$connectedUser = $this->getUser();

			// If user already has an avatar, delete it
			if ( $connectedUser->getAvatar() != null )
			{
				unlink($avatarDir . $connectedUser->getAvatar());
			}

			// Generate a new random filename until success
			do
			{
				$newFileName = $connectedUser->getId() . '-' . md5(random_bytes(100)) . '.' . $avatar->guessExtension();
			}
			while ( file_exists($avatarDir . $newFileName) );

			// Update DB
			$connectedUser->setAvatar($newFileName);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			// Move the avatar in the correct directory
			$avatar->move($avatarDir, $newFileName);

			// Success flash message
			$this->addFlash('success', 'Avatar mis à jours');

			// Redirect to 'main_profil' page
			return $this->redirectToRoute('main_profil');
		}


		return $this->render('main/profil-edit-avatar.html.twig',
			[
				'editAvatarForm' => $editAvatarForm->createView(),
			]);
	}


	/**
	 * Controller for the avatar suppression page
	 * @Route("/profil/avatar/delete/", name="main_avatar_delete")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function avatarDelete(Request $request): Response
	{
		$deleteAvatarForm = $this->createForm(DeleteAvatarFormType::class);
		$deleteAvatarForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $deleteAvatarForm->getClickedButton() === $deleteAvatarForm->get('cancel') )
		{
			return $this->redirectToRoute('main_profil');
		}

		// If Confirm button is clicked
		if ( $deleteAvatarForm->isSubmitted() )
		{
			// Get location of avatar directory
			$avatarDir = $this->getParameter('users_uploaded_avatar_dir');
			//Get connected user
			$connectedUser = $this->getUser();

			// Delete avatar
			unlink($avatarDir . $connectedUser->getAvatar());

			// Update DB
			$connectedUser->setAvatar(null);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Avatar supprimé');

			// Redirect to 'main_profil' page
			return $this->redirectToRoute('main_profil');
		}


		return $this->render('main/profil-delete-avatar.html.twig',
			[
				'deleteAvatarForm' => $deleteAvatarForm->createView(),
			]);
	}
}
