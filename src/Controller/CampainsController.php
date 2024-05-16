<?php

namespace App\Controller;

use App\Entity\Campains;
use App\Form\CampainsType;
use App\Repository\CampainAssociationRepository;
use App\Repository\CampainsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\CampainEmailSender;
use App\Service\TestEmailSender;

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
    #[Route('/result/{id<[0-9]+>}', name: 'result', methods: ['GET', 'POST'])]
    public function result(
        Campains $campain,
        CampainAssociationRepository $campainAssoRepo
    ): Response {
        $campainAssociations = $campainAssoRepo->findByCampains($campain);
        $oldCampainAssociations = "";
        if ($campain->getOldcampain()) {
            $oldCampainAssociations = $campainAssoRepo->findByCampains($campain->getOldcampain());
        }

        return $this->render('admin/campains/result.html.twig', [
            'campain'                   => $campain,
            'campainAssociations'       => $campainAssociations,
            'oldCampainAssociations'    => $oldCampainAssociations,
        ]);
    }

    #[Route('/responses/{campain<[0-9]+>}', name: 'responses', methods: ['GET', 'POST'])]
    public function associations_responses(
        Campains $campain,
        CampainAssociationRepository $campainAssoRepo
    ): Response {
        $campainAssociations = $campainAssoRepo->findByCampains($campain);

        $campainAssociations = $campainAssoRepo->findBy([
            'statut' => 'updated',
            'campains' => $campain
        ]);
        $oldCampainAssociations = "";
        if ($campain->getOldcampain()) {
            $oldCampainAssociations = $campainAssoRepo->findByCampains($campain->getOldcampain());
        }

        return $this->render('admin/campains/associations_responses.html.twig', [
            'campain'                   => $campain,
            'campainAssociations'       => $campainAssociations,
            'oldCampainAssociations'    => $oldCampainAssociations,
        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        CampainsRepository $campainsRepository,
        Campains $campain = null
    ): Response {
        if ($campain === null) {
            $campain = new Campains();
        }

        $form = $this->createForm(CampainsType::class, $campain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $campain->setValid(true);
            $entityManager->persist($campain);
            $entityManager->flush();

            $campainsRepository->setAllValidToFalseExcept($campain);

            return $this->redirectToRoute('campains_show', ['id' => $campain->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/campains/edit.html.twig', [
            'campain'   => $campain,
            'form'      => $form,
        ]);
    }

    #[Route('/play/{id<[0-9]+>}', name: 'play', methods: ['GET', 'POST'])]
    /**
     * Envoie email pour campagne avec token
     *
     * @param Request $request
     * @param Campains $campain
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function campains_play(
        Campains $campain,
        EntityManagerInterface $entityManager,
        CampainEmailSender $campainEmailSender
    ): Response {
        // verifie si toutes les données necessaires à l'email sont renseignees
        if (
            $campain->getObjetEmail() === null
            || $campain->getTexteEmail() === null
            || $campain->getDestinataire() === null
        ) {
            $this->addFlash('warning', 'Vous devez renseigner tous les champs nécessaires');
            return $this->redirectToRoute('campains_edit', ['id' => $campain->getId()]);
        }

        // Vérifier si la campagne a déjà été envoyée
        // if ($campain->getDateSend() !== null) {
        //     $this->addFlash('warning', 'La campagne a déjà été envoyée.');
        // } else {
        // Envoyer l'e-mail aux destinataires
        $campainEmailSender->sendEmailToDestinataires($campain);

        // Mettre à jour le statut de la campagne pour indiquer qu'elle a été envoyée
        $campain->setDateSend(new \DateTime());
        $entityManager->persist($campain);
        $entityManager->flush();

        $this->addFlash('success', 'La campagne a été envoyée avec succès.');

        return $this->redirectToRoute('campains_result', ['id' => $campain->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/replay/{id<[0-9]+>}', name: 'replay', methods: ['GET', 'POST'])]
    /**
     * Envoie email pour campagne avec token
     *
     * @param Request $request
     * @param Campains $campain
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function campains_replay(
        Campains $campain,
        EntityManagerInterface $entityManager,
        CampainAssociationRepository $campainAssoRepo,
        CampainEmailSender $campainEmailSender
    ): Response {
        // verifie si toutes les données necessaires à l'email sont renseignees
        if (
            $campain->getObjetEmail() === null
            || $campain->getTexteEmail() === null
            || $campain->getDestinataire() === null
        ) {
            $this->addFlash('warning', 'Vous devez renseigner tous les champs nécessaires');
            return $this->redirectToRoute('campains_edit', ['id' => $campain->getId()]);
        }
        $associationsenAttente = array_map(function ($campainAssociation) {
            return $campainAssociation->getAssociation();
        }, $campainAssoRepo->findBy([
            'statut' => 'send',
            'campains' => $campain
        ]));

        $campainEmailSender->sendEmailToDestinataires($campain, $associationsenAttente);

        // Mettre à jour le statut de la campagne pour indiquer qu'elle a été envoyée
        // $campain->setDateSend(new \DateTime());
        $entityManager->persist($campain);
        $entityManager->flush();

        $this->addFlash('success', 'La campagne a été renvoyée avec succès.');

        return $this->redirectToRoute('campains_result', ['id' => $campain->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/envoyer-email-test', name: 'envoyer_email_test', methods: ['GET', 'POST'])]
    public function envoyerEmailTest(TestEmailSender $service): Response
    {
        $service->envoyerEmailTest('maryonete26@gmail.com');

        return new Response('E-mail de test envoyé !');
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
