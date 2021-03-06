<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use \DateTime;
use App\Entity\User;
use App\Form\UserRegistrationType;
use App\Recaptcha\RecaptchaValidator;
use Symfony\Component\Form\FormError;
use Symfony\Component\String\Slugger\SluggerInterface;


class RegistrationController extends AbstractController
{
	/**
	 * @Route("/inscription/", name="main_register")
	 */
	public function register(Request $request, UserPasswordHasherInterface $passwordHasher, RecaptchaValidator $recaptcha, SluggerInterface $slugger): Response
	{
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

				//We get the avatar field
				$avatar = $registrationForm->get('avatar')->getData();

				//Condition if the avatar field is used, since it is not required
				if ( $avatar )
				{
					$originalFileName = pathinfo($avatar->getClientOriginalName(), PATHINFO_FILENAME);
					$safeFileName = $slugger->slug($originalFileName);
					$newFileName = $safeFileName . '-' . uniqid() . '.' . $avatar->guessExtension();

					$avatar->move(
						$this->getParameter('users_uploaded_avatar_dir'),
						$newFileName
					);
				}

				$user
					->setPassword(
						$passwordHasher->hashPassword(
							$user,
							$registrationForm->get('plainPassword')->getData()
						)
					)
					->setActive(true)
					->setRoles(["ROLE_USER"])
					->setRegistrationDate(new DateTime());

				if ( $avatar )
				{
					$user
						->setAvatar($newFileName);
				}

				//We use the entity manager to save the new user in the database
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();

				//Success message when the account has been created and the user has been registered
				$this->addFlash('success', 'Votre compte a bien ??t?? cr????');

				//We redirect the user to the login page
				return $this->redirectToRoute('main_login');
			}
		}

		return $this->render('registration/register.html.twig', ['registrationForm' => $registrationForm->createView(),]);
	}
}
