<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
	/**
	 * Controller for the login page
	 * @Route("/connexion/", name="main_login")
	 */
	public function login(AuthenticationUtils $authenticationUtils): Response
	{
		// Use this ?
		// usually you'll want to make sure the user is authenticated first
		// $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		// https://symfony.com/doc/current/security.html#b-authenticating-your-users

		// Redirect to the 'main_home' page if user is already logged-in
		if ( $this->getUser() ) // Return the user from $_SESSION (connected)
		{
			return $this->redirectToRoute('main_home');
		}

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();
		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
	}


	/**
	 * Controller for the logout page
	 * @Route("/deconnexion/", name="main_logout")
	 */
	public function logout()
	{
		// This code will never be read as logout is internally managed by the security bundle
		throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
	}
}
