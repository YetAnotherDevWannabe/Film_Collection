<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
	/**
	 * Controller for the register page
	 * @Route("/inscription/", name="main_register")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, RecaptchaValidator $recaptcha): Response
	{
		// Force redirection if user is already connected
		if ( $this->getUser() )
		{
			return $this->redirectToRoute('main_home');
		}

		// Create a new user
		$user = new User();
		// Create a new form that plugs itself on the newly created user
		$form = $this->createForm(RegistrationFormType::class, $user);
		// Hydrate the form with the $request object ( $_POST[] )
		$form->handleRequest($request);

		// If form is submitted
		if ( $form->isSubmitted() )
		{

			// Get the Google ReCAPTCHA response from the form ( $_POST['g-captcha-response'] )
			$captchaResponse = $request->request->get('g-recaptcha-response', null);

			// Check if Google ReCAPTCHA response is correct
			if ( !$recaptcha->verify($captchaResponse, $request->server->get('REMOTE_ADDR')) || $captchaResponse == null )
			{
				$form->addError(new FormError('Veuillez remplir le captcha de sécurité'));
			}

			// If no error
			if ( $form->isValid() )
			{
				// Hydrate the user with the missing data (not coming from the form)
				$user
					->setActive(true)
					->setRegistrationDate(new \DateTime())
					->setPassword(
						$passwordEncoder->encodePassword(
							$user,
							$form->get('plainPassword')->getData()
						)
					);

				// Save the new user in the DB via the entity manager
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();

				// Success flash message
				$this->addFlash('success', 'Votre compte a bien été créé.');

				// Redirect the user to the 'main_home' page
				return $this->redirectToRoute('main_login');

			}
		}

		return $this->render('registration/register.html.twig',
			[
				'registrationForm' => $form->createView(),
			]);
	}
}
