<?php

namespace App\Controller;

use App\Entity\{User, Referent, President, Association, CampainAssociation, Campains};
use App\Form\AssociationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\{AssociationRepository, CampainAssociationRepository};
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\{TextareaType, SubmitType};
use Symfony\Component\Form\FormFactoryInterface;

#[Route('/association', name: 'asso_')]
class AssociationController extends AbstractController
{
    #[Route('', name: 'list')]
    public function listAsso(AssociationRepository $assoRepo): Response
    {
        return $this->render('admin/association/list.html.twig', [
            'listAsso'  =>  $assoRepo->findBy([], ['code' => 'ASC'])
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    public function show(Association $association, CampainAssociationRepository $campainAssoRepo): Response
    {
        return $this->render('admin/association/show.html.twig', [
            'association'   => $association,
            'campains'      => $campainAssoRepo->findByAssociation($association, ['id' => 'DESC'])
        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function new_update(
        Request $request,
        EntityManagerInterface $entityManager,
        Association $association = null
    ): Response {
        // Create a new association if none provided
        $association = $association ?? new Association();

        // Create the form for the association
        $form = $this->createForm(AssociationType::class, $association, [
            'current_association_id' => $association->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $request->request->all();

            // Check if the association code already exists for another association
            $existingAssociation =
                $entityManager->getRepository(Association::class)
                ->findOneByCode($association->getCode());
            if ($existingAssociation && $existingAssociation->getId() !== $association->getId()) {
                $this->addFlash('warning', 'Le code association existe déjà pour une autre association.');
                return $this->redirectToRoute('asso_edit', ['id' => $association->getId()], Response::HTTP_SEE_OTHER);
            }
            // Indicateur president logic
            $this->handlePresident($association, $entityManager, $requestData);

            // Indicateur referent logic
            $this->handleReferent($association, $entityManager, $requestData);

            $entityManager->persist($association);
            $entityManager->flush();

            return $this->redirectToRoute('asso_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/association/edit.html.twig', [
            'association'   => $association,
            'form'          => $form,
        ]);
    }

    // Separate function to handle president creation/assignment
    private function handlePresident(
        Association $association,
        EntityManagerInterface $entityManager,
        array $requestData
    ): void {

        $presidentData = '';
        if (!empty($requestData['association']['president'])) {
            $presidentData = $requestData['association']['president'];
        } else {
            $presidentData = $requestData['president_new'];
        }
        if ($presidentData) {
            $president = $this->createPresident($association, $entityManager, $presidentData);
            $association->setPresident($president);
        }
    }

    // Separate function to handle referent creation/assignment
    private function handleReferent(
        Association $association,
        EntityManagerInterface $entityManager,
        array $requestData
    ): void {
        $referentData = '';
        if (!empty($requestData['association']['referent'])) {
            $referentData = $requestData['association']['referent'];
        } else {
            $referentData = $requestData['referent_new'];
        }
        if ($referentData) {
            $referent = $this->createReferent($association, $entityManager, $referentData);
            $association->setReferent($referent);
        }
    }

    // Function to create a president entity
    private function createPresident(
        Association $association,
        EntityManagerInterface $entityManager,
        $presidentData
    ): ?President {
        // Validation de l'entrée
        if (is_string($presidentData) && ctype_digit($presidentData)) {
            $userId = (int) $presidentData;
            $user = $entityManager->getRepository(User::class)->findOneById($userId);

            if (!$user) {
                $this->addFlash('error', 'L\'identifiant utilisateur fourni est invalide.');
                return $this->redirectToRoute('asso_edit', ['id' => $association->getId()], Response::HTTP_SEE_OTHER);
            }
            // Recherche d'un président existant pour cet utilisateur
            $president = $entityManager->getRepository(President::class)->findOneByUser($user);
            if ($president) {
                if ($president->getAssociation() !== null && $president->getAssociation() !== $association) {
                    $this->addFlash('error', 'Le président sélectionné est déjà associé à une association.');
                    return $this->redirectToRoute('asso_edit', ['id' => $association->getId()], Response::HTTP_SEE_OTHER);
                }
                return $president; // Président existant, non affecté
            }
            // Création d'un nouveau président
            $president = new President();
            $president->setUser($user);
            $president->setFonction("");
        } else {
            // Création d'un nouveau président avec les données fournies
            $user = new User();
            $user->setFirstname($presidentData['user']['firstname']);
            $user->setLastname($presidentData['user']['lastname']);
            $user->setEmail($presidentData['user']['email']);
            $entityManager->persist($user);

            $president = new President();
            $president->setUser($user);
            $president->setFonction($presidentData['fonction']);
        }

        $president->setAssociation($association);
        $entityManager->persist($president);

        return $president;
    }

    private function createReferent(
        Association $association,
        EntityManagerInterface $entityManager,
        $referentData
    ): Referent {
        if (is_string($referentData) && ctype_digit($referentData)) {
            $userId = (int) $referentData;
            $user = $entityManager->getRepository(User::class)->findOneById($userId);

            if (!$user) {
                $this->addFlash('error', 'L\'identifiant utilisateur fourni pour le référent est invalide.');
                return $this->redirectToRoute('asso_edit', ['id' => $association->getId()], Response::HTTP_SEE_OTHER);
            }

            // Recherche d'un referent existant pour cet utilisateur
            $referent = $entityManager->getRepository(Referent::class)->findOneByUser($user);

            if ($referent) {
                if ($referent->getAssociation() !== null and $referent->getAssociation() != $association) {
                    $this->addFlash('error', 'Le référent sélectionné est déjà associé à une association.');
                    return $this->redirectToRoute('asso_edit', ['id' => $association->getId()], Response::HTTP_SEE_OTHER);
                }

                return $referent; // referent existant, non affecté
            }

            // Création d'un nouveau président
            $referent = new Referent();
            $referent->setUser($user);
            $referent->setTel("");
        } else {
            $user = new User();
            $user->setFirstname($referentData['user']['firstname']);
            $user->setLastname($referentData['user']['lastname']);
            $user->setEmail($referentData['user']['email']);
            $entityManager->persist($user);

            $referent = new Referent();
            $referent->setUser($user);
            $referent->setTel($referentData['tel']);
            // Add referent to the association

        }
        $referent->setAssociation($association);
        $association->setReferent($referent);
        $entityManager->persist($referent);
        return $referent;
    }



    #[Route('/texte/{id<[0-9]+>}', name: 'editTextePersonnalise', methods: ['GET', 'POST'])]
    /**
     * Modifier le texte personnalise d'une asso pour une campagne
     *
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param CampainAssociation $campAsso
     * @return void
     */
    public function editTextePersonnalise(
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        CampainAssociation $campAsso
    ) {

        $form = $formFactory->createBuilder()
            ->add('texte_personnalise', TextareaType::class, [
                'label' => 'Texte de présentation pour la campagne ',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5
                ],
                'data' => $campAsso->getTextePersonnalise()
            ])
            ->getForm();
        $oldCampainAssociations = "";
        if ($campAsso && $campAsso->getCampains()->getOldcampain()) {
            $oldCampainAssociations = $entityManager->getRepository(CampainAssociation::class)->findOneBy(
                [
                    'campains' => $campAsso->getCampains()->getOldcampain(),
                    'association' => $campAsso->getAssociation()
                ]
            );
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textePersonnalise = $form->get('texte_personnalise')->getData();

            // Définir la nouvelle valeur du champ texte personnalisé sur l'objet CampainAssociation
            $campAsso->setTextePersonnalise($textePersonnalise);
            $entityManager->persist($campAsso);
            $entityManager->flush();

            return $this->redirectToRoute('asso_home');
        }

        return $this->render('/admin/edit_texte_personnalise.html.twig', [
            'form' => $form->createView(),
            'campAsso' => $campAsso,
            'oldCampainAssociations' => $oldCampainAssociations
        ]);
    }
    #[Route('/textes/{id<[0-9]+>}', name: 'editAllTextePersonnalise', methods: ['GET', 'POST'])]
    public function editAllTextePersonnalise(
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        CampainAssociationRepository $campainAssoRepo,
        Campains $campain
    ) {
        $campainAssociations = $entityManager->getRepository(CampainAssociation::class)
            ->findByCampains($campain);

        $oldCampainAssociations = "";
        if ($campain->getOldcampain()) {
            $oldCampainAssociations =
                $campainAssoRepo->findByCampains($campain->getOldcampain());
        }
        $formBuilder = $formFactory->createBuilder();
        foreach ($campainAssociations as $campAsso) {


            $formBuilder->add('texte_personnalise' . $campAsso->getAssociation()->getId(), TextareaType::class, [
                'label' => '',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 15
                ],
                'data' => $campAsso ? $campAsso->getTextePersonnalise() : null
            ]);
        }
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); // Récupérer les données soumises du formulaire
            foreach ($campainAssociations as $campAsso) {
                $fieldName = 'texte_personnalise' . $campAsso->getAssociation()->getId();
                $textePersonnalise = $data[$fieldName]; // Récupérer le texte personnalisé pour cette association
                // Mettre à jour l'objet CampainAssociation avec le nouveau texte personnalisé
                $campAsso->setTextePersonnalise($textePersonnalise);
                $entityManager->persist($campAsso);
            }
            $entityManager->flush();
            return $this->redirectToRoute('asso_home');
        }

        return $this->render('/admin/edit_all_texte_personnalise.html.twig', [
            'form' => $form->createView(),
            'campainAssociations' => $campainAssociations,
            'campain' => $campain,
            'oldCampainAssociations' => $oldCampainAssociations,
        ]);
    }
}
