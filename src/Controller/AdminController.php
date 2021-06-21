<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 * @Security ("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(): Response
	{
		return $this->render('admin/index.html.twig');
	}
}
