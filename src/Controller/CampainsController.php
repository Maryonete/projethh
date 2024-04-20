<?php

namespace App\Controller;

use App\Entity\Campains;
use App\Form\CampainsType;
use App\Entity\CampainAssociation;
use App\Form\CampainAssociationType;
use App\Repository\CampainsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/campains', name: 'campains_')]
class CampainsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CampainsRepository $campainsRepository): Response
    {
        return $this->render('admin/campains/index.html.twig', [
            'campains' => $campainsRepository->findAll(),
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    public function show(Campains $campain): Response
    {
        return $this->render('admin/campains/show.html.twig', [
            'campain' => $campain,
        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        Campains $campain = null
    ): Response {
        if ($campain === null) {
            $campain = new Campains();
        }

        $form = $this->createForm(CampainsType::class, $campain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($campain);
            $entityManager->flush();

            return $this->redirectToRoute('campains_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campains/edit.html.twig', [
            'campain'   => $campain,
            'form'      => $form,
        ]);
    }

    #[Route('/play/{id<[0-9]+>}', name: 'play', methods: ['GET', 'POST'])]
    public function campains_play(
        Request $request,
        Campains $campain,

        EntityManagerInterface $entityManager
    ): Response {


        $campainAsso = new CampainAssociation();
        $form = $this->createForm(CampainAssociationType::class, $campainAsso);
        $form->handleRequest($request);
        return $this->render('admin/campains/play.html.twig', [
            'form'      => $form,
            'campain'   =>  $campain,

        ]);
    }
    #[Route('/{id<[0-9]+>}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Campains $campain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $campain->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($campain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin/index', [], Response::HTTP_SEE_OTHER);
    }
}
