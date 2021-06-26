<?php

namespace App\Controller;

use App\Entity\Collect;
use App\Entity\Film;
use App\Entity\User;
use App\Entity\Avatar;
use App\Form\AvatarEditFormType;
use App\Form\AvatarDeleteFormType;
use App\Form\EditPasswordFormType;
use App\Form\ProfilDeleteFormType;
use App\Form\DisableAccountFormType;
use App\Form\ProfilEditFormType;
use App\Form\RegistrationFormType;
use App\Form\ContactFormType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Component\Form\FormError;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Form\EditProfileFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * @Route("/", name="main_")
 */
class MainController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(): Response
	{
		// $this->addFlash('error', 'error');
		// $this->addFlash('warning', 'warning');
		// $this->addFlash('info', 'info');
		// $this->addFlash('success', 'success');

		$filmRepo = $this->getDoctrine()->getRepository(Film::class);
		$CollectRepo = $this->getDoctrine()->getRepository(Collect::class);

		$hompageEntityNumber = $this->getParameter('hompage_entity_number');

		$films = $filmRepo->findBy([], ['id' => 'DESC'], $hompageEntityNumber);
		$collects = $CollectRepo->findBy([], ['id' => 'DESC'], $hompageEntityNumber);

		return $this->render('main/index.html.twig',
			[
				'films'    => $films,
				'collects' => $collects,
			]);
	}


	/**
	 * @Route("/profil/", name="profil")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profil(): Response
	{
		return $this->render('main/profil.html.twig');
	}


	/**
	 * @Route("/profil/edit/", name="profil_edit")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profilEdit(Request $request, UserPasswordHasherInterface $passwordHasher): Response
	{
		$editDataForm = $this->createForm(EditProfileFormType::class);
		$editDataForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $editDataForm->getClickedButton() === $editDataForm->get('cancel') )
		{
			return $this->redirectToRoute('main_profil');
		}

		//Conditions if the form was successfully filled in
		if ( $editDataForm->isSubmitted() && $editDataForm->isValid() )
		{
			$user = $this->getUser();

			$oldPasswordField = $editDataForm->get('oldPassword');
			if ( !$oldPasswordField->isEmpty() )
			{
				$oldPassword = $request->request->get('edit_profile_form')['oldPassword'];
				if ( !$passwordHasher->isPasswordValid($user, $oldPassword) )
				{
					$this->addFlash('error', 'Le mot de passe n\'est pas correct');
					return $this->redirectToRoute('main_profil');
				}
				else
				{
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

					//We use the entity manager to save changes
					$em = $this->getDoctrine()->getManager();
					$em->flush($user);

					$this->addFlash('success', 'Vos modifications ont bien été prises en compte');
					return $this->redirectToRoute('main_profil');
				}
			}
		}

		return $this->render('main/edit.html.twig', [
			'editDataForm' => $editDataForm->createView(),
		]);
	}


	/**
	 * @Route("/profil/password/edit/", name="profil_password_edit")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profilPasswordEdit(Request $request, UserPasswordHasherInterface $passwordHasher): Response
	{
		$editPasswordForm = $this->createForm(EditPasswordFormType::class);
		$editPasswordForm->handleRequest($request);

		//Conditions if the form was successfully filled in
		if ( $editPasswordForm->isSubmitted() && $editPasswordForm->isValid() )
		{
			$user = $this->getUser();
			$oldPassword = $request->request->get('edit_password_form')['oldPassword'];

			if ( $passwordHasher->isPasswordValid($user, $oldPassword) )
			{
				$passwordField = $editPasswordForm->get('newPassword');
				if ( !$passwordField->isEmpty() )
				{
					$user->setPassword(
						$passwordHasher->hashPassword(
							$user,
							$editPasswordForm->get('newPassword')->getData()
						)
					);
				}

				//We use the entity manager to save changes
				$em = $this->getDoctrine()->getManager();
				$em->flush();
				$em->refresh($user);

				//Success message
				$this->addFlash('success', 'Vos modifications ont bien été prises en compte');

				// return $this->redirectToRoute('main_profil');
				return $this->redirectToRoute('main_logoff');
			}
			else
			{
				$this->addFlash('error', 'Votre ancien mot de passe est incorrect.');
			}
		}

		return $this->render('main/edit-password.html.twig', [
			'editPasswordForm' => $editPasswordForm->createView(),
		]);
	}


	/**
	 * @Route("/profil/delete/", name="profil_delete")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function profilDelete(Request $request): Response
	{

		$profilDeleteForm = $this->createForm(DisableAccountFormType::class);
		$profilDeleteForm->handleRequest($request);

		// If Cancel button is clicked
		if ( $profilDeleteForm->getClickedButton() === $profilDeleteForm->get('cancel') )
		{
			return $this->redirectToRoute('main_profil');
		}

		// If Confirm button is clicked
		if ( $profilDeleteForm->isSubmitted() && $profilDeleteForm->isValid() )
		{

			//Get connected user
			$connectedUser = $this->getUser();
			// Deactivate account in DB but keep it in the DB
			$connectedUser->setActive(false);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			// Success flash message
			$this->addFlash('success', 'Votre compte a bien été supprimé');

			// Redirect to 'main_home' page
			return $this->redirectToRoute('main_logout');
		}

		return $this->render('main/profil-delete.html.twig',
			[
				'profilDeleteForm' => $profilDeleteForm->createView(),
			]);
	}


	/**
	 * @Route("/profil/edit/avatar/", name="avatar_edit")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function editAvatar(Request $request): Response
	{
		$avatarForm = $this->createForm(AvatarEditFormType::class);
		$avatarForm->handleRequest($request);

		if ( $avatarForm->isSubmitted() && $avatarForm->isValid() )
		{
			$avatar = $avatarForm->get('avatar')->getData();
			$profilAvatarDir = $this->getParameter('users_uploaded_avatar_dir');
			$connectedUser = $this->getUser();

			if($connectedUser->getAvatar() != null)
			{
				unlink( $profilAvatarDir . $connectedUser->getAvatar() );
			}

			do
			{
				$newFileName = md5($connectedUser->getId() . random_bytes(100)) . '.' . $avatar->guessExtension();
			}
			while ( file_exists($profilAvatarDir . $newFileName) );

			$connectedUser->setAvatar($newFileName);
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$avatar->move($profilAvatarDir, $newFileName);

			$this->addFlash('succès', 'Votre avatar a été modifié !');
			return $this->redirectToRoute('main_profil');
		}

		return $this->render('main/avatar.edit.html.twig', [
			'avatarForm' => $avatarForm->createView(),
		]);

	}


	/**
	 * @Route("/profil/avatar/delete/", name="avatar_delete")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function avatarDelete(Request $request): Response
	{

		$connectedUser = $this->getUser();

		if ( $connectedUser->getAvatar() == null )
		{
			return $this->redirectToRoute('main_profil');
		}

		$form = $this->createForm(AvatarDeleteFormType::class);

		$form->handleRequest($request);

		// Si le bouton annuler est cliqué

		if ( $form->getClickedButton() == $form->get('cancel') )
		{
			return $this->redirectToRoute('main_profil');
		}

		if ( $form->isSubmitted() && $form->isValid() )
		{

			// Suppression de l'avatar
			$profilAvatarDir = $this->getParameter('users_uploaded_avatar_dir');

			// Suppression de l'image
			unlink($profilAvatarDir . $connectedUser->getAvatar());

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
	 * @Route("/copyright/", name="copyright")
	 */
	public function copyright(): Response
	{
		return $this->render('main/copyright.html.twig');
	}


	/**
	 * @Route("/contact/", name="contact")
	 */
	public function contact(Request $request, RecaptchaValidator $recaptcha, MailerInterface $mailer): Response
	{
		// Création d'un nouveau formulaire et d'un nouveau message de contact
		$contactForm = $this->createForm(ContactFormType::class);

		// Symfony va remplir $newContact grâce aux données du formulaire envoyé
		$contactForm->handleRequest($request);

		// Pour savoir si le formulaire a été envoyé et validé, on a accès à cette condition :
		if ( $contactForm->isSubmitted() )
		{

			// Si le captcha n'est pas valide, on crée une nouvelle erreur dans le formulaire
			if ( !$recaptcha->verify($request->request->get('g-recaptcha-response', ""), $request->server->get('REMOTE_ADDR')) )
			{

				// Ajout d'une nouvelle erreur manuellement dans le formulaire
				$contactForm->addError(new FormError('Veuillez valider le captcha !'));
			}

			if ( $contactForm->isValid() )
			{

				$email = ( new TemplatedEmail() )
					->from(new Address('noreply@moviebrary.fr', 'noreply'))
					->to($contactForm->get("email")->getData())
					->subject($contactForm->get("subject")->getData())
					->htmlTemplate('email/contact.html.twig')    // Fichier twig du mail en version html
					->textTemplate('email/contact.txt.twig')     // Fichier twig du mail en version text

					->context([
						'contact_email' => $contactForm->get("email")->getData(),
						'content'       => $contactForm->get("content")->getData(),
					]);
				// Envoi du mail
				$mailer->send($email);

				// Création d'un message flash de type "success"
				$this->addFlash('success', 'Votre message de contact a bien été envoyé, nous vous répondrons dans les meilleurs délais !');

				// Redirection de l'utilisateur
				return $this->redirectToRoute('main_contact');

			}

		}

		//Pour que la vue puisse afficher le formulaire, on doit lui envoyer le formulaire généré, avec $formContact->createView()
		return $this->render('main/contact.html.twig', [
			'contactForm' => $contactForm->createView(),
		]);
	}
}