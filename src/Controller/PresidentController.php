<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\President;
use App\Form\PresidentType;
use App\Repository\PresidentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/president', name: 'president_')]
class PresidentController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(PresidentRepository $presidentRepository): Response
    {
        return $this->render('admin/user/president/index.html.twig', [
            'admin/user/presidents' => $presidentRepository->findAll(),
        ]);
    }
    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    /**
     * Visualiser les infos du president
     *
     * @param President $president
     * @return Response
     */
    public function show(President $president): Response
    {
        return $this->render('admin/user/president/show.html.twig', [
            'president' => $president,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    /**
     * Create / Update a President
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response,
     *
     */
    public function new_update(
        Request $request,
        EntityManagerInterface $entityManager,
        President $president = null
    ): Response {
        if (null === $president) {
            $president = new President();
        }
        $form = $this->createForm(PresidentType::class, $president);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash(
                'success',
                'Opération effectuée avec succès !'
            );
            $entityManager->persist($president);
            $entityManager->flush();

            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/president/edit.html.twig', [
            'president' => $president,
            'form'      => $form,
        ]);
    }




    #[Route('/{id<[0-9]+>}', name: 'delete', methods: ['POST'])]
    /**
     * Delete president
     *
     * @param Request $request
     * @param President $president
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, President $president, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $president->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($president);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
