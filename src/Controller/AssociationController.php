<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Referent;
use App\Entity\President;
use App\Entity\Association;
use App\Form\AssociationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
    public function show(Association $association): Response
    {
        return $this->render('admin/association/show.html.twig', [
            'association' => $association,
        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function new_update(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Association $association = null
    ): Response {
        if ($association === null) {
            $association = new Association();
        }
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $request->request->all();
            if ($association->getPresident() == null) {
                $presidentData = $requestData['president_new'];

                if ($presidentData) {
                    $user = new User();
                    $user->setFirstname($presidentData['user']['firstname']);
                    $user->setLastname($presidentData['user']['lastname']);
                    $user->setEmail($presidentData['user']['email']);

                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            'tttttttt'
                        )
                    );
                    $entityManager->persist($user);
                    $president = new President();
                    $president->setUser($user);
                    $president->setFonction($presidentData['fonction']);
                    $entityManager->persist($president);
                    // Ajout du président à l'association
                    $association->setPresident($president);
                    $entityManager->flush();
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

                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                'tttttttt'
                            )
                        );
                        $entityManager->persist($user);
                        $referent = new Referent();
                        $referent->setUser($user);
                        $referent->setTel($referentData['tel']);
                        $entityManager->persist($referent);
                        // Ajout du referent à l'association
                        $association->setreferent($referent);
                        $entityManager->flush();
                    }
                }
            }
            // if ($association->getReferent() == null) {
            //     $association->setReferent($request->get('referent'));
            // }
            dump($association);
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
}
