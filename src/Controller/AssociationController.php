<?php

namespace App\Controller;

use App\Entity\{User, Referent, President, Association, CampainAssociation, Campains};
use App\Form\AssociationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\{AssociationRepository, CampainAssociationRepository};
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactoryInterface;

#[Route('/association', name: 'asso_')]
class AssociationController extends AbstractController
{
    #[Route('', name: 'list')]
    public function listAsso(AssociationRepository $assoRepo): Response
    {
        return $this->render('admin/association/list.html.twig', [
            'listAsso'  =>  $assoRepo->findBy([], ['code' => 'ASC']),
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
        } elseif (isset($requestData['referent_new'])) {
            $referentData = $requestData['referent_new'];
        }
        if ($referentData) {
            $referent = $this->createReferent($association, $entityManager, $referentData);
            $association->setReferent($referent);
        } else {
            $association->setReferent(null);
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
                    return $this->redirectToRoute(
                        'asso_edit',
                        ['id' => $association->getId()],
                        Response::HTTP_SEE_OTHER
                    );
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
                if ($referent->getAssociation() !== null && $referent->getAssociation() != $association) {
                    $this->addFlash('error', 'Le référent sélectionné est déjà associé à une association.');
                    return $this->redirectToRoute(
                        'asso_edit',
                        ['id' => $association->getId()],
                        Response::HTTP_SEE_OTHER
                    );
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
     * Modifier le texte personnalisé d'une asso pour une campagne
     *
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param CampainAssociation $campAsso
     * @return Response
     */
    public function editTextePersonnalise(
        Request $request,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        CampainAssociation $campAsso
    ): Response {
        $form = $this->createTextePersonnaliseForm($formFactory, $campAsso);
        $oldCampainAssociations = $this->getOldCampainAssociations($entityManager, $campAsso);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveTextePersonnalise($form, $campAsso, $entityManager);
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
    ): Response {
        $campainAssociations = $campainAssoRepo->findByCampains($campain);
        $oldCampainAssociations = $campain->getOldcampain()
            ? $campainAssoRepo->findByCampains($campain->getOldcampain())
            : [];

        $form = $this->createAllTextePersonnaliseForm($formFactory, $campainAssociations);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveAllTextePersonnalise($form, $campainAssociations, $entityManager);
            return $this->redirectToRoute('asso_home');
        }

        return $this->render('/admin/edit_all_texte_personnalise.html.twig', [
            'form' => $form->createView(),
            'campainAssociations' => $campainAssociations,
            'campain' => $campain,
            'oldCampainAssociations' => $oldCampainAssociations,
        ]);
    }

    // Méthodes utilitaires

    private function createTextePersonnaliseForm(FormFactoryInterface $formFactory, CampainAssociation $campAsso)
    {
        return $formFactory->createBuilder()
            ->add('texte_personnalise', TextareaType::class, [
                'label' => 'Texte de présentation pour la campagne',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 5],
                'data' => $campAsso->getTextePersonnalise()
            ])
            ->getForm();
    }

    private function getOldCampainAssociations(EntityManagerInterface $entityManager, CampainAssociation $campAsso)
    {
        if ($campAsso->getCampains()->getOldcampain()) {
            return $entityManager->getRepository(CampainAssociation::class)->findOneBy([
                'campains' => $campAsso->getCampains()->getOldcampain(),
                'association' => $campAsso->getAssociation()
            ]);
        }
        return null;
    }

    private function saveTextePersonnalise($form, CampainAssociation $campAsso, EntityManagerInterface $entityManager)
    {
        $textePersonnalise = $form->get('texte_personnalise')->getData();
        $campAsso->setTextePersonnalise($textePersonnalise);
        $entityManager->persist($campAsso);
        $entityManager->flush();
    }

    private function createAllTextePersonnaliseForm(FormFactoryInterface $formFactory, array $campainAssociations)
    {
        $formBuilder = $formFactory->createBuilder();
        foreach ($campainAssociations as $campAsso) {
            $formBuilder->add('texte_personnalise' . $campAsso->getAssociation()->getId(), TextareaType::class, [
                'label' => '',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 15],
                'data' => $campAsso->getTextePersonnalise()
            ]);
        }
        return $formBuilder->getForm();
    }

    private function saveAllTextePersonnalise($form, array $campainAssociations, EntityManagerInterface $entityManager)
    {
        $data = $form->getData();
        foreach ($campainAssociations as $campAsso) {
            $fieldName = 'texte_personnalise' . $campAsso->getAssociation()->getId();
            $textePersonnalise = $data[$fieldName];
            $campAsso->setTextePersonnalise($textePersonnalise);
            $entityManager->persist($campAsso);
        }
        $entityManager->flush();
    }


    #[Route('/{id<[0-9]+>}/markAsObsolete', name: 'markAsObsolete', methods: ['GET'])]
    /**
     * Marque une association comme obsolète.
     * 
     */
    public function markAsObsolete(
        EntityManagerInterface $entityManager,
        $id
    ): Response {
        $association = $entityManager->getRepository(Association::class)->find($id);

        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }
        $president = $association->getPresident();
        if ($president) {
            $president->setAssociation(null);
        }
        $referent = $association->getReferent();
        if ($referent) {
            $referent->setAssociation(null);
        }
        $association->setStatus('obsolete');

        $entityManager->flush();


        return $this->redirectToRoute('asso_list');
    }
    #[Route('/{id<[0-9]+>}/reactivate', name: 'reactivate', methods: ['GET'])]
    /**
     * Réactive une association obsolète.
     * 
     */
    public function reactivate(
        EntityManagerInterface $entityManager,
        $id
    ): Response {
        $association = $entityManager->getRepository(Association::class)->find($id);

        if (!$association) {
            throw $this->createNotFoundException('Association non trouvée');
        }

        $association->setStatus('active');

        $entityManager->flush();


        return $this->redirectToRoute('asso_list');
    }
}
