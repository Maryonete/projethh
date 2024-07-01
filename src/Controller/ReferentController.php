<?php

namespace App\Controller;

use App\Entity\Referent;
use App\Form\ReferentType;
use App\Repository\TracesRepository;
use App\Repository\ReferentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/referent', name: 'referent_')]
class ReferentController extends AbstractController
{
    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    /**
     * View Referent infos
     *
     * @param Referent $referent
     * @return Response
     */
    public function show(
        TracesRepository $histoRepo,
        Request $request,
        Referent $referent = null
    ): Response {

        if (null === $referent) {
            $this->addFlash('error', 'Le référent fourni est invalide.');
            // Redirection vers la page précédente
            $referer = $request->headers->get('referer');
            return $this->redirect($referer ?: $this->generateUrl('asso_home'));
        }
        return $this->render('admin/user/referent/show.html.twig', [
            'referent' => $referent,
            'historiesRef' => $histoRepo->findBy(
                ['user' => $referent->getUser(), 'role' => 'referent'],
                ['startDate' => 'ASC']
            ),
            'historiesPres' => $histoRepo->findBy(
                ['user' => $referent->getUser(), 'role' => 'president'],
                ['startDate' => 'ASC']
            )

        ]);
    }
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[Route('/{id<[0-9]+>}/edit', name: 'edit', methods: ['GET', 'POST'])]
    /**
     * Create or Edit Referent
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param Referent|null $referent
     * @return Response
     */
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager,
        ReferentRepository $referentRepository,
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
}
