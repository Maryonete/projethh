<?php

namespace App\Controller;

use App\Entity\President;
use App\Form\PresidentType;
use App\Repository\PresidentRepository;
use App\Repository\ReferentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user')]
class UserController extends AbstractController
{
    // PRESIDENT
    #[Route('/', name: '', methods: ['GET'])]
    public function president_index(
        PresidentRepository $presidentRepository,
        ReferentRepository $referentRepository
    ): Response {
        return $this->render('admin/user/index.html.twig', [
            'presidents' => $presidentRepository->findAll(),
            'referents' => $referentRepository->findAll(),
        ]);
    }
}
