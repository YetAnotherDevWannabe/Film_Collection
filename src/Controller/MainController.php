<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Form\AvatarEditFormType;
use App\Form\AvatarDeleteFormType;
use App\Form\ProfilDeleteFormType;
use App\Form\ProfilEditFormType;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Form\EditProfileFormType;
use App\Form\DisableAccountFormType;


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
	 * @Route("/profil/edit/", name="main_profil_edit")
	 */
	public function profilEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
	{

		//This is a form to modify data
		$editDataForm = $this->createForm(EditProfileFormType::class);

		//We fill the form with  POST data obtained from $request object
		$editDataForm->handleRequest($request);

		//Conditions if the form was succesffully filled in
		if ( $editDataForm->isSubmitted() )
		{
			if ( $editDataForm->isValid() )
			{

				$user = $this->getUser();


				/*$user
					->setNickname($editDataForm->get('nickname')->getData())
					->setEmail($editDataForm->get('email')->getData())
					->setPassword(
					$passwordEncoder->encodePassword(
						$user,
						$editDataForm->get('password')->getData()
					)
				)
				;*/


				//We test below if any field is not blank, we set the new data filled in by the current connected user

				$emailField = $editDataForm->get('email');

				if ( !$emailField->isEmpty() )
				{
					$user->setEmail($emailField->getData());
				}

				$nicknameField = $editDataForm->get('nickname');

				if ( !$nicknameField->isEmpty() )
				{
					$user->setNickname($nicknameField->getData());
				}

				$passwordField = $editDataForm->get('password');

				if ( !$passwordField->isEmpty() )
				{
					$user->setPassword(
						$passwordEncoder->encodePassword(
							$user,
							$editDataForm->get('password')->getData()
						)
					);

				}

                //We use the entity manager to save changes
                $em = $this->getDoctrine()->getManager();

                $em->flush();

				$em->refresh($user);

                //Success message when the user successfully edited at least one field
				if(!$emailField->isEmpty() || !$nicknameField->isEmpty() || !$passwordField->isEmpty()){
					$this->addFlash('success', 'Vos modifications ont bien été prises en compte');
				}

				return $this->redirectToRoute('main_profil');

			}

		}

		return $this->render('main/edit.html.twig', [
			'editDataForm' => $editDataForm->createView(),
		]);
	}


	/**
	 * Controller for the profil suppression page
	 * @Route("/profil/delete/", name="main_profil_delete")
	 */
	public function profilDelete(TokenStorageInterface $tokenStorage, Request $request): Response
	{

		$form = $this->createForm(DisableAccountFormType::class);

		$form->handleRequest($request);

		if($form->getClickedButton() == $form->get('cancel')){
			return $this->redirectToRoute('main_profil');
		}

		if($form->isSubmitted() && $form->isValid()){
			$user = $this->getUser();

			$user
				->setActive(false);

			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$tokenStorage->setToken();

			$this->addFlash('success', 'Votre compte a bien été supprimé');

			return $this->redirectToRoute('main_home');
		}


		return $this->render('main/profil-delete.html.twig', [
			'form' => $form->createView()
		]);
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

			do
			{
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
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function avatarDelete(Request $request): Response
	{

		$connectedUser = $this->getUser();

		if($connectedUser->getAvatar() == null){
			$this->addFlash('warning', 'Vous n\'avez pas d\'avatar à supprimer actuellement !');
			return $this->redirectToRoute('main_profil');
		}

		$form = $this->createForm(AvatarDeleteFormType::class);

		$form->handleRequest($request);

		// Si le bouton annuler est cliqué

		if($form->getClickedButton() == $form->get('cancel')){
			return $this->redirectToRoute('main_profil');
		}

		if($form->isSubmitted() && $form->isValid()){

			// Suppression de l'avatar
			$profilAvatarDir = $this->getParameter('users_uploaded_avatar_dir');

			// Suppression de l'image
			unlink( $profilAvatarDir . $connectedUser->getAvatar() );

			// Suppression du nom de l'image dans l'utilisateur
			$connectedUser->setAvatar(null);
			$em = $this->getDoctrine()->getManager();
			$em->flush();
			$this->addFlash('success', 'L\'avatar a été supprimé avec succès !');
			return $this->redirectToRoute('main_profil');

		}


		return $this->render('main/avatar.delete.html.twig', [
			'avatarDeleteForm' => $form->createView(),
		]);
	}


	/**
	 * Controller for the copyright page
	 * @Route("/copyright/", name="main_copyright")
	 */
	public function copyright(): Response
	{
		return $this->render('main/copyright.html.twig');
	}
}
