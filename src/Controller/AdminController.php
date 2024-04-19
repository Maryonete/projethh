<?php

namespace App\Controller;

use App\Repository\PresidentRepository;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/user', name: 'user', methods: ['GET'])]
    public function index_user(PresidentRepository $presidentRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'presidents' => $presidentRepository->findAll(),
        ]);
    }
}
