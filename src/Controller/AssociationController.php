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
            'listAsso'  =>  $assoRepo->findAll([], ['code' => 'ASC'])
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
        if ($association === null) {
            $association = new Association();
        }
        $form = $this->createForm(AssociationType::class, $association, [
            'current_association_id' => $association->getId(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $request->request->all();
            // ajout president
            if ($association->getPresident() == null) {
                $presidentData = $requestData['president_new'];
                if ($presidentData) {
                    // le president existe dejà
                    $otherPresident = $entityManager->getRepository(President::class)
                        ->findOneByEmail($presidentData['user']['email']);
                    if ($otherPresident) {
                        // le president existe ET n'est pas rattaché à une association
                        if ($otherPresident->getAssociation() === null) {
                            $otherPresident->setAssociation($association);
                            $entityManager->persist($otherPresident);
                            // Ajout du président à l'association
                            $association->setPresident($otherPresident);
                        } elseif ($otherPresident->getAssociation()->getId() !== $association->getId()) {
                            $this->addFlash(
                                'warning',
                                'Une président d\'une autre association existe déjà avec cette adresse e-mail'
                            );
                            return $this->redirectToRoute(
                                'edit',
                                ['id' => $association],
                                Response::HTTP_SEE_OTHER
                            );
                        }
                    } else {
                        //
                        $user = new User();
                        $user->setFirstname($presidentData['user']['firstname']);
                        $user->setLastname($presidentData['user']['lastname']);
                        $user->setEmail($presidentData['user']['email']);


                        $entityManager->persist($user);
                        $president = new President();
                        $president->setUser($user);
                        $president->setFonction($presidentData['fonction']);
                        $president->setAssociation($association);
                        $entityManager->persist($president);
                        $association->setPresident($president);
                    }
                }
            }
            if ($association->getReferent() == null) {

                if (isset($requestData['referent_new'])) {
                    $referentData = $requestData['referent_new'];

                    if ($referentData) {
                        $user = new User();
                        $user->setFirstname($referentData['user']['firstname']);
                        $user->setLastname($referentData['user']['lastname']);
                        $user->setEmail($referentData['user']['email']);
                        $entityManager->persist($user);
                        $referent = new Referent();
                        $referent->setUser($user);
                        $referent->setAssociation($association);
                        $referent->setTel($referentData['tel']);
                        $entityManager->persist($referent);
                        // Ajout du referent à l'association
                        $association->setreferent($referent);
                        $entityManager->flush();
                    }
                }
            }
            $entityManager->persist($association);
            $entityManager->flush();

            return $this->redirectToRoute('asso_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/association/edit.html.twig', [
            'association'   => $association,
            'form'          => $form,
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Association $association, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $association->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($association);
            $entityManager->flush();
        }

        return $this->redirectToRoute('asso_list', [], Response::HTTP_SEE_OTHER);
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
