<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Form\CampainAssociationType;
use App\Repository\ReferentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use App\Entity\{User, Referent, President, CampainAssociation};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CampainAssociationRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('', name: 'association')]
class VerifyDataController extends AbstractController
{
    #[Route('/assoDeclineCampain/{token}', name: '_declineCampain')]
    public function assoDeclineCampain(
        string $token,
        EntityManagerInterface $entityManager,
        CampainAssociationRepository $campAssoRepo
    ): Response {
        $campAsso = $this->findCampAssoByToken($token, $campAssoRepo);
        if (!$campAsso) {
            // Cas où l'association de campagne n'existe pas
            return $this->renderError404();
        }
        $campAsso->setStatut('declined');
        $campAsso->setUpdatedTextAt(new DateTimeImmutable());
        $updatedBy = $this->isGranted('IS_AUTHENTICATED_FULLY') ? 'administrateur' : 'referent';
        $campAsso->setUpdatedBy($updatedBy);

        $entityManager->persist($campAsso);
        $entityManager->flush();
        return $this->render('verif_ok.html.twig', ['campAsso' => $campAsso]);
    }

    #[Route('/associationhh/{token}', name: 'hh')]
    public function verifyData(
        Request $request,
        String $token,
        EntityManagerInterface $entityManager,
        ReferentRepository $referentRepository,
        CampainAssociationRepository $campAssoRepo
    ): Response {
        $campAsso = $this->findCampAssoByToken($token, $campAssoRepo);

        // Initialisation de la réponse par défaut
        $response = null;

        if (!$campAsso) {
            // Cas où l'association de campagne n'existe pas
            $response = $this->renderError404();
        } elseif ($campAsso->getStatut() !== 'send') {
            // Cas où le statut n'est pas 'send'
            $response = $this->renderVerifOk($campAsso);
        } else {
            // Cas où le statut est 'send' et l'association de campagne existe
            $oldCampainAssociations = $this->findOldCampainAssociations($campAsso, $campAssoRepo);
            $form = $this->createForm(CampainAssociationType::class, $campAsso);
            $isPresidentReferent = $this->checkIsPresidentReferent($campAsso);

            $form->add('presidentIsReferent', CheckboxType::class, [
                'label' => 'Le président(e) est également le référent(e)',
                'mapped' => false,
                'required' => false,
                'data' => $isPresidentReferent,
            ]);

            $form->handleRequest($request);
            if ($form->isSubmitted() && !$form->isValid()) {
                $this->addFormErrorsToFlash($form);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $this->processFormSubmission($request, $campAsso, $entityManager, $referentRepository);
                $response = $this->renderVerifOk($campAsso);
            } else {
                $response = $this->renderVerifyData($campAsso, $form, $oldCampainAssociations, $isPresidentReferent);
            }
        }

        return $response;
    }
    /**
     * Retrouve une association de campagne par son jeton.
     *
     * @param string $token Le jeton de l'association de campagne à retrouver.
     * @param CampainAssociationRepository $campAssoRepo Le référentiel des associations de campagne.
     * @return CampainAssociation|null L'association de campagne correspondante, ou null si aucune n'est trouvée.
     */
    private function findCampAssoByToken(
        string $token,
        CampainAssociationRepository $campAssoRepo
    ): ?CampainAssociation {
        return $campAssoRepo->findOneBy(['token' => $token]);
    }
    /**
     * Rend la page d'erreur 404.
     *
     * @return Response La réponse HTTP contenant la page d'erreur 404.
     */
    private function renderError404(): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }
    /**
     * Rend la page de vérification OK.
     *
     * @param CampainAssociation $campAsso L'association de campagne à afficher sur la page.
     * @return Response La réponse HTTP contenant la page de vérification OK.
     */
    private function renderVerifOk(CampainAssociation $campAsso): Response
    {
        return $this->render('verif_ok.html.twig', ['campAsso' => $campAsso]);
    }
    /**
     * Recherche les anciennes associations de campagne liées à une association de campagne donnée.
     *
     * @param CampainAssociation $campAsso L'association
     * de campagne pour laquelle rechercher les anciennes associations.
     * @param CampainAssociationRepository $campAssoRepo Le référentiel des associations de campagne.
     * @return CampainAssociation|null L'ancienne association de campagne trouvée, ou null si aucune n'est trouvée.
     */
    private function findOldCampainAssociations(
        CampainAssociation $campAsso,
        CampainAssociationRepository $campAssoRepo
    ): ?CampainAssociation {
        return $campAsso && $campAsso->getCampains()->getOldcampain() ?
            $campAssoRepo->findOneBy(
                [
                    'campains' => $campAsso->getCampains()->getOldcampain(),
                    'association' => $campAsso->getAssociation()
                ]
            ) : null;
    }
    /**
     * Vérifie si le président de l'association est également le référent.
     *
     * @param CampainAssociation $campAsso L'association de campagne pour laquelle vérifier le président et le référent.
     * @return bool true si le président est également le référent, sinon false.
     */
    private function checkIsPresidentReferent(CampainAssociation $campAsso): bool
    {
        return $campAsso->getAssociation()->getReferent() !== null &&
            $campAsso->getAssociation()->getPresident()->getUser()->getEmail() ==
            $campAsso->getAssociation()->getReferent()->getUser()->getEmail();
    }
    /**
     * Ajoute les erreurs de formulaire à la session flash.
     *
     * @param FormInterface $form Le formulaire contenant les erreurs à ajouter.
     * @return void
     */
    private function addFormErrorsToFlash(FormInterface $form): void
    {
        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('warning', $error->getMessage());
        }
    }

    /**
     * Traite la soumission du formulaire de campagne.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param CampainAssociation $campAsso L'association de campagne associée au formulaire.
     * @param EntityManagerInterface $entityManager L'interface pour interagir avec la base de données.
     * @param ReferentRepository $referentRepository Le référentiel des référents.
     * @return void
     */
    private function processFormSubmission(
        Request $request,
        CampainAssociation $campAsso,
        EntityManagerInterface $entityManager,
        ReferentRepository $referentRepository
    ): void {
        $requestData = $request->request->all();

        if (isset($requestData['president_new'])) {
            $this->processNewPresident($requestData['president_new'], $campAsso, $entityManager);
        }

        if (isset($requestData['campain_association2']['presidentIsReferent']) || isset($requestData['referent_new'])) {
            $this->processReferent($requestData, $campAsso, $entityManager, $referentRepository);
        }

        $campAsso->setStatut('updated');
        $campAsso->setUpdatedTextAt(new DateTimeImmutable());
        $updatedBy = $this->isGranted('IS_AUTHENTICATED_FULLY') ? 'administrateur' : 'referent';
        $campAsso->setUpdatedBy($updatedBy);

        $entityManager->persist($campAsso);
        $entityManager->flush();
    }
    private function processNewPresident(
        array $presidentData,
        CampainAssociation $campAsso,
        EntityManagerInterface $entityManager
    ): void {
        $otherPresident = $entityManager->getRepository(President::class)
            ->findOneByEmail($presidentData['user']['email']);

        if ($otherPresident && $otherPresident->getAssociation()->getId() !== $campAsso->getAssociation()->getId()) {
            $this->addFlash('warning', 'Un président d\'une autre association existe déjà avec cette adresse e-mail');
            return;
        }

        $user = (new User())
            ->setFirstname($presidentData['user']['firstname'])
            ->setLastname($presidentData['user']['lastname'])
            ->setEmail($presidentData['user']['email']);
        $entityManager->persist($user);

        $president = (new President())
            ->setUser($user)
            ->setFonction($presidentData['fonction']);
        $entityManager->persist($president);

        $campAsso->getAssociation()->setPresident($president);
    }
    /**
     * Traite les données du référent pour une association de campagne.
     *
     * @param array $requestData Les données de la requête HTTP.
     * @param CampainAssociation $campAsso L'association de campagne associée au référent.
     * @param EntityManagerInterface $entityManager L'interface pour interagir avec la base de données.
     * @param ReferentRepository $referentRepository Le référentiel des référents.
     * @return void
     */
    private function processReferent(
        array $requestData,
        CampainAssociation $campAsso,
        EntityManagerInterface $entityManager,
        ReferentRepository $referentRepository
    ): void {
        $referent = $referentRepository->findOneBy(['user' => $campAsso->getAssociation()->getPresident()->getUser()]);
        if (!$referent) {
            $referent = (new Referent())->setUser($campAsso->getAssociation()->getPresident()->getUser());
        }
        $campAsso->getAssociation()->setReferent($referent);
        $referent->setTel($requestData['referent_new']['tel'] ?? '');
        $entityManager->persist($referent);
    }
    /**
     * Rend la vue pour vérifier les données de l'association de campagne.
     *
     * @param CampainAssociation $campAsso L'association de campagne à vérifier.
     * @param FormInterface $form Le formulaire associé à l'association de campagne.
     * @param CampainAssociation|null $oldCampainAssociations Les anciennes associations de campagne, s'il y en a.
     * @param bool $isPresidentReferent Indique si le président est également le référent.
     * @return Response La réponse HTTP avec le rendu de la vue de vérification des données.
     */
    private function renderVerifyData(
        CampainAssociation $campAsso,
        FormInterface $form,
        ?CampainAssociation $oldCampainAssociations,
        bool $isPresidentReferent
    ): Response {
        return $this->render('verify_data.html.twig', [
            'campAsso'              => $campAsso,
            'form'                  => $form,
            'oldCampainAssociations' => $oldCampainAssociations,
            'isPresidentReferent'   => $isPresidentReferent,
        ]);
    }
}
