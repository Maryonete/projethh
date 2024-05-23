<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestFlashController extends AbstractController
{
    // Contrôleur pour tester les messages flash et 
    // la gestion des formulaires
    #[Route('/flash', name: 'flash')]
    public function testFlash(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'valider',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump('formulaire valide');
            $this->addFlash('success', 'Message flash a afficher');
            return $this->redirectToRoute('flashok');
        }
        return $this->render('tests/test.html.twig', [
            'form' => $form,
        ]);
    }
    // Action pour afficher la page de réussite après 
    // la soumission du formulaire
    #[Route('/flashok', name: 'flashok')]
    public function testFlashOK(): Response
    {
        return $this->render('tests/index.html.twig', []);
    }
}
