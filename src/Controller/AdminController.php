<?php

namespace App\Controller;

use App\Form\AdminType;
use App\Form\AdminPasswordType;
use App\Service\StatsCalculator;
use App\Repository\UserRepository;
use App\Repository\CampainsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CampainAssociationRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/asso', name: 'asso_')]
class AdminController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(
        AssociationRepository $assoRepo,
        CampainsRepository $campainRepo,
        CampainAssociationRepository $campainAssoRepo,
        StatsCalculator $statsCalculator

    ): Response {
        $campain = $campainRepo->findOneByValid(true);
        $campainAssociations = [];
        $oldCampainAssociations = "";

        if ($campain) {
            $campainAssociations = $campainAssoRepo->findByCampains($campain);
            if ($campain->getOldcampain()) {
                $oldCampainAssociations = $campainAssoRepo->findByCampains($campain->getOldcampain());
            }
        }
        $stat = $statsCalculator->calculateStats($campain, $campainAssoRepo);

        return $this->render('admin/index.html.twig', [
            'listAsso'              =>  $assoRepo->findAll(),
            'campain'               =>  $campain,
            'campainAssociations'   =>  $campainAssociations,
            'oldCampainAssociations' => $oldCampainAssociations,
            'stat'                  => $stat,
        ]);
    }

    #[Route('/admin', name: 'admin')]
    public function admin(): Response
    {
        return $this->render('admin/dashboard.html.twig', []);
    }
    #[Route('/admin_edit', name: 'admin_edit', methods: ['GET', 'POST'])]
    public function admin_edit(
        EntityManagerInterface $entityManager,
        Request $request,
        UserRepository $userRepository
    ): Response {
        $admin = $userRepository->findOneByRole('ROLE_ADMIN');

        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Les modifications ont été enregistrées avec succès.');

            return $this->redirectToRoute('asso_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/administrateur/edit.html.twig', [
            'user' => $admin,
            'form' => $form,
        ]);
    }
    #[Route('/admin_editpwd', name: 'admin_editpwd', methods: ['GET', 'POST'])]
    public function admin_editpwd(
        EntityManagerInterface $entityManager,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $hasher
    ): Response {


        $form = $this->createForm(AdminPasswordType::class);
        $form->handleRequest($request);
        // $session = $request->getSession();
        // if ($form->isSubmitted() && !$form->isValid()) {
        //     $errors = $form->getErrors(true, false);
        //     dump($errors);
        // }
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $admin */
            $admin = $userRepository->findOneByRole('ROLE_ADMIN');
            if ($hasher->isPasswordValid($admin, $form->getData()['plainPassword'])) {
                $admin->setPlainPassword(
                    $form->getData()['newPassword']
                );
                $entityManager->flush();
                $this->addFlash('success', 'Le mot de passe a été modifié avec succès.');
                return $this->redirectToRoute('asso_home');
            } else {

                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect.'
                );
                return $this->redirectToRoute('asso_admin_editpwd');
            }
        }
        return $this->render(
            'admin/user/administrateur/editPassword.html.twig',
            [
                'form' => $form,
            ]
        );
    }
}
