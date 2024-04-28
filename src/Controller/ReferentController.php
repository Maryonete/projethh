<?php

namespace App\Controller;

use App\Entity\Referent;
use App\Form\ReferentType;
use App\Repository\ReferentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/referent', name: 'referent_')]
class ReferentController extends AbstractController
{
    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    public function show(Referent $referent): Response
    {
        return $this->render('admin/user/referent/show.html.twig', [
            'referent' => $referent,
        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Referent $referent = null
    ): Response {
        if (null === $referent) {
            $referent = new Referent();
        }
        $form = $this->createForm(ReferentType::class, $referent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($referent);
            $entityManager->flush();
            return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/referent/edit.html.twig', [
            'referent' => $referent,
            'form' => $form,
        ]);
    }

    #[Route('/{id<[0-9]+>}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Referent $referent, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $referent->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($referent);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user', [], Response::HTTP_SEE_OTHER);
    }
}
