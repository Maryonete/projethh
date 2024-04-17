<?php

namespace App\Controller;

use App\Repository\AssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/asso', name: 'asso_')]
class AdminController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(AssociationRepository $assoRepo): Response
    {
        return $this->render('admin/index.html.twig', [
            'listAsso'  =>  $assoRepo->findBy([], ['code' => 'ASC'])
        ]);
    }
}
