<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CollectController extends AbstractController
{
    /**
     * @Route("/collect", name="collect")
     */
    public function index(): Response
    {
        return $this->render('collect/index.html.twig', [
            'controller_name' => 'CollectController',
        ]);
    }
}
