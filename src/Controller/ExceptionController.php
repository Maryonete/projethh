<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ExceptionController extends AbstractController
{
    public function renderNotFoundException(Request $request): Response
    {
        // Personnalisez la page 404 ici
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [
            'request' => $request,
        ]);
    }
}
