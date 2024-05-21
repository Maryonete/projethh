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

#[Route('/campains', name: 'campains_')]
class CampainsController extends AbstractController
{
    /**
     * Affiche la liste de toutes les campagnes.
     *
     * @param CampainsRepository $campainsRepository
     * @return Response
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CampainsRepository $campainsRepository): Response
    {
        return $this->render('admin/campains/index.html.twig', [
            'campains' => $campainsRepository->findAll(),
        ]);
    }
    /**
     * Affiche les détails d'une campagne spécifique.
     */
    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    public function show(Campains $campain): Response
    {
        return $this->render('admin/campains/show.html.twig', [
            'campain' => $campain,
        ]);
    }
    /**
     * Affiche les résultats d'une campagne spécifique.
     */
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
    /**
     * Affiche les réponses des associations à une campagne spécifique.
     */
    #[Route('/responses/{campain<[0-9]+>}', name: 'responses', methods: ['GET', 'POST'])]
    public function associations_responses(
        Campains $campain,
        CampainAssociationRepository $campainAssoRepo
    ): Response {
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
    /**
     *  Création/modification campagne
     */
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
    /**
     *  relance campagne
     */
    #[Route('/{id<[0-9]+>}/relance', name: 'relance', methods: ['GET', 'POST'])]
    public function relance(
        Request $request,
        EntityManagerInterface $entityManager,
        CampainsRepository $campainsRepository,
        CampainAssociationRepository $campainAssoRepo,
        Campains $campain,
        CampainEmailSender $campainEmailSender
    ): Response {

        $form = $this->createForm(
            CampainsType::class,
            $campain,
            [
                'exclude_fields' => ['libelle', 'date', 'oldcampain']
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->handleCampain($campain, $entityManager, $campainEmailSender, $campainAssoRepo);
        }

        return $this->render('admin/campains/relance.html.twig', [
            'campain'   => $campain,
            'form'      => $form,
        ]);
    }
    /**
     * Envoie email pour campagne avec token
     *
     * @param Campains $campain
     * @param EntityManagerInterface $entityManager
     * @param CampainEmailSender $campainEmailSender
     * @return Response
     */
    #[Route('/play/{id<[0-9]+>}', name: 'play', methods: ['GET', 'POST'])]
    public function campains_play(
        Campains $campain,
        EntityManagerInterface $entityManager,
        CampainEmailSender $campainEmailSender
    ): Response {
        return $this->handleCampain($campain, $entityManager, $campainEmailSender);
    }


    /**
     * Gère l'envoi de la campagne par email
     *
     * @param Campains $campain
     * @param EntityManagerInterface $entityManager
     * @param CampainEmailSender $campainEmailSender
     * @param CampainAssociationRepository|null $campainAssoRepo
     * @return Response
     */
    private function handleCampain(
        Campains $campain,
        EntityManagerInterface $entityManager,
        CampainEmailSender $campainEmailSender,
        CampainAssociationRepository $campainAssoRepo = null
    ): Response {
        // Vérifie si toutes les données nécessaires à l'email sont renseignées
        if (
            $campain->getObjetEmail() === null
            || $campain->getTexteEmail() === null
            || $campain->getDestinataire() === null
        ) {
            $this->addFlash('warning', 'Vous devez renseigner tous les champs nécessaires');
            return $this->redirectToRoute('campains_edit', ['id' => $campain->getId()]);
        }

        if ($campainAssoRepo) {
            $associationsenAttente = array_map(function ($campainAssociation) {
                return $campainAssociation->getAssociation();
            }, $campainAssoRepo->findBy([
                'statut' => 'send',
                'campains' => $campain
            ]));
            $campainEmailSender->sendEmailToDestinataires($campain, $associationsenAttente);
        } else {
            $campainEmailSender->sendEmailToDestinataires($campain);
            $campain->setDateSend(new \DateTime());
        }

        $entityManager->persist($campain);
        $entityManager->flush();

        $this->addFlash('success', 'La campagne a été envoyée avec succès.');

        return $this->redirectToRoute('campains_result', ['id' => $campain->getId()], Response::HTTP_SEE_OTHER);
    }
}
