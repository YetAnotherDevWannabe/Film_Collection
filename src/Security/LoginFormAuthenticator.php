<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
	use TargetPathTrait;
	public const LOGIN_ROUTE = 'main_login';
	private UrlGeneratorInterface $urlGenerator;
	private $session;
	private $security;

	public function __construct(
		UrlGeneratorInterface $urlGenerator,
		SessionInterface $session,
		Security $security
	)
	{
		$this->urlGenerator = $urlGenerator;
		$this->session = $session;
		$this->security = $security;
	}


	public function authenticate(Request $request): PassportInterface
	{
		$email = $request->request->get('email', '');

		$request->getSession()->set(Security::LAST_USERNAME, $email);

		return new Passport(
			new UserBadge($email),
			new PasswordCredentials($request->request->get('password', '')),
			[
				new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
			]
		);
	}


	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		if ( $targetPath = $this->getTargetPath($request->getSession(), $firewallName) )
		{
			return new RedirectResponse($targetPath);
		}

		// Get user to check if account activated
		$user = $this->security->getUser();

		if ( !$user->getActive() )
		{
			// Error flash message
			$this->session->getFlashBag()->add('error', 'Compte désactivé');
			// Redirect the user to the 'main_logout' page after connection
			return new RedirectResponse($this->urlGenerator->generate('main_logout'));
		}
		else
		{
			// Success flash message
			$this->session->getFlashBag()->add('success', 'Connexion réussie !');
			// Redirect the user to the 'main_home' page after connection
			return new RedirectResponse($this->urlGenerator->generate('main_home'));
		}
	}


	protected function getLoginUrl(Request $request): string
	{
		return $this->urlGenerator->generate(self::LOGIN_ROUTE);
	}
}