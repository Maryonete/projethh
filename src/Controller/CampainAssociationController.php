<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CampainAssociationController extends AbstractController
{
    #[Route('/campain/association', name: 'app_campain_association')]
    public function index(): Response
    {
        return $this->render('campain_association/index.html.twig', [
            'controller_name' => 'CampainAssociationController',
        ]);
    }
}
