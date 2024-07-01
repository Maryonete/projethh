<?php

namespace App\Controller;

use App\Entity\President;
use App\Form\PresidentType;
use App\Repository\PresidentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\TracesRepository;

#[Route('/president', name: 'president_')]
class PresidentController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        PresidentRepository $presidentRepository
    ): Response {
        return $this->render('admin/user/president/index.html.twig', [
            'admin/user/presidents' => $presidentRepository->findAll(),

        ]);
    }
    #[Route('/{id<[0-9]+>}', name: 'show', methods: ['GET'])]
    /**
     * View president infos
     *
     * @param President $president
     * @return Response
     */
    public function show(
        TracesRepository $histoRepo,
        Request $request,
        President $president = null
    ): Response {
        if (null === $president) {
            $this->addFlash('error', 'Le président fourni est invalide.');
            // Redirection vers la page précédente
            $referer = $request->headers->get('referer');
            return $this->redirect($referer ?: $this->generateUrl('asso_home'));
        }
        return $this->render('admin/user/president/show.html.twig', [
            'president' => $president,
            'historiesRef' => $histoRepo->findBy(
                ['user' => $president->getUser(), 'role' => 'referent'],
                ['startDate' => 'ASC']
            ),
            'historiesPres' => $histoRepo->findBy(
                ['user' => $president->getUser(), 'role' => 'president'],
                ['startDate' => 'ASC']
            )
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
}
