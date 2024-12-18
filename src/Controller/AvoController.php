<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvoController extends AbstractController
{
    /**
     * @Template("default/index.html.twig")
     * This will let React be our Frontend
     * @Route("/{reactRouting}", name="home", priority="-1", requirements={"reactRouting"="^(?!api).+"}, defaults={"reactRouting": null})
     */
    #[Route('/{reactRouting}', name: 'home', priority: '-1', requirements: ['reactRouting' => '^(?!api).+'], defaults: ['reactRouting' => null])]
    public function index(): Response
    {
        return $this->render('avo/index.html.twig');
    }
}
