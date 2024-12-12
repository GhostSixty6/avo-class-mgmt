<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AvoController extends AbstractController
{
    #[Route('/avo', name: 'home')]
    public function index(): Response
    {
        return $this->render('avo/index.html.twig', [
            'controller_name' => 'AvoController',
        ]);
    }

        /**
     * @Route("/api/classes", name="classes")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    #[Route('/api/classes', name: 'classes')]
    public function getClasses()
    {
        $classes = [
            [
                'id' => 1,
                'name' => 'Grade 1',
            ],
            [
                'id' => 2,
                'name' => 'Grade 2',
            ],
            [
                'id' => 3,
                'name' => 'Grade 3',
            ],
            [
                'id' => 4,
                'name' => 'Grade 4',
            ],
            [
                'id' => 5,
                'name' => 'Grade 5',
            ],
            [
                'id' => 6,
                'name' => 'Grade 6',
            ],
            [
                'id' => 7,
                'name' => 'Grade 7',
            ]
        ];
    
        $response = new Response();

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode($classes));
        
        return $response;
    }
}
