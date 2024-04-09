<?php

namespace App\Controller;

use App\File\FileUploader;
use App\Form\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/import_xlsx', name: 'import_xlsx')]
    public function import_xlsx(
        EntityManagerInterface $em,
        Request $request,
        FileUploader $uploader
    ): Response {

        $form = $this->createForm(FileType::class);
        $form->add('save', SubmitType::class, [
            'label' => "Importer le fichier xlsx",
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var File $file */
            $file = $form->getData();

            if ($uploadedFile = $form->get('file')->getData()) {
                $filename = $uploader->upload($uploadedFile);
                $file->setFilename($filename);
            }

            $em->persist($file);
            $em->flush();
        }
        return $this->render('admin/import_xlsx.html.twig', [
            'form' => $form
        ]);
    }
}
