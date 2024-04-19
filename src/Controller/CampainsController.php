<?php

namespace App\Controller;

use App\Entity\Campains;
use App\Form\CampainsType;
use App\Repository\CampainsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/campains')]
class CampainsController extends AbstractController
{
    #[Route('/', name: 'app_campains_index', methods: ['GET'])]
    public function index(CampainsRepository $campainsRepository): Response
    {
        return $this->render('campains/index.html.twig', [
            'campains' => $campainsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_campains_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campain = new Campains();
        $form = $this->createForm(CampainsType::class, $campain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campain);
            $entityManager->flush();

            return $this->redirectToRoute('app_campains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campains/new.html.twig', [
            'campain' => $campain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campains_show', methods: ['GET'])]
    public function show(Campains $campain): Response
    {
        return $this->render('campains/show.html.twig', [
            'campain' => $campain,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campains_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campains $campain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampainsType::class, $campain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_campains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('campains/edit.html.twig', [
            'campain' => $campain,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_campains_delete', methods: ['POST'])]
    public function delete(Request $request, Campains $campain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campain->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($campain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_campains_index', [], Response::HTTP_SEE_OTHER);
    }
}
