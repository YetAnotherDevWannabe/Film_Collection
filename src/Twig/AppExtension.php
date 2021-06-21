<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AppExtension extends AbstractExtension
{
	private $session;

	public function __construct(SessionInterface $session){
		$this->session = $session;
	}

	public function getFilters(): array
	{
		return [
			// If your filter generates SAFE HTML, you should add a third
			// parameter: ['is_safe' => ['html']]
			// Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
			new TwigFilter('count_size', [$this, 'countSize']),    // Création d' un filtre appelé "count_size" dans twig, qui appellera la méthode "countSize"
			new TwigFilter('excerpt', [$this, 'excerpt']),         // Création d' un filtre appelé "excerpt" dans twig, qui appellera la méthode "excerpt"
		];
	}

	/**
	 * Méthode branchée sur le filtre Twig "count_size"
	 * Cette méthode n' accepte en paramètre que des chaînes de texte et doit retourner obligatoirement un nombre
	 */
	public function size(string $value): int
	{
		// On retourne la taille de la chaîne de texte
		return mb_strlen($value);
	}

	/**
	 * Filtre qui retournera la chaîne de texte donnée tronquée à $maxSize caractères avec "..." concaténé derrière.
	 * Si trop petite le filtre retourne juste la chaîne sans y toucher
	 */
	public function excerpt(string $value, int $maxSize): string
	{
		return mb_strimwidth($value, 0, $maxSize + 4, ' ...');
	}


	public function getFunctions(): array
	{
		return [
			new TwigFunction('flash_message', [$this, 'flashMessage']),
		];
	}

	/**
	 * Méthode pour faire des messages flash depuis la vue Twig
	 */
	public function flashMessage(string $flashType, $message)
	{
		$this->session->getFlashBag()->add($flashType, $message);
	}
}
