<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use \DateTime;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Component\Form\FormError;


class RegistrationController extends AbstractController
{
	/**
	 * @Route("/inscription/", name="main_register")
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, RecaptchaValidator $recaptcha): Response
	{
		//TODO: avatar ne marche pas
		//This is the redirection of the user to homepage if he is already connected
		if ( $this->getUser() )
		{
			return $this->redirectToRoute('main_home');
		}

		$user = new User();
		$registrationForm = $this->createForm(UserRegistrationType::class, $user);
		$registrationForm->handleRequest($request);

		//Conditions if the form was successfully filled in
		if ( $registrationForm->isSubmitted() )
		{
			if ( !$recaptcha->verify($request->request->get('g-recaptcha-response'), $request->server->get('REMOTE_ADDR')) )
			{
				$registrationForm->addError(new formError('Veuillez valider le captcha.'));
			}

			if ( $registrationForm->isValid() )
			{
				$user
					->setPassword(
						$passwordEncoder->encodePassword(
							$user,
							$registrationForm->get('plainPassword')->getData()
						)
					)
					->setActive(true)
					->setRoles(["ROLE_USER"])
					->setRegistrationDate(new DateTime())
				;

				//We use the entity manager to save the new user in the database
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();

				//Success message when the account has been created and the user has been registered
				$this->addFlash('success', 'Votre compte a bien été bien créé');

				//We redirect the user to the login page
				return $this->redirectToRoute('main_login');
			}
		}

		return $this->render('registration/register.html.twig', ['registrationForm' => $registrationForm->createView(),]);
	}
}
