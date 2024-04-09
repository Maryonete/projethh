<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use App\Entity\File;
use App\File\FileUploader;
use App\Form\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\MimeTypes;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EntityManagerInterface $em): Response
    {
        try {


            // Chargement du fichier
            $filePath = $this->getParameter('kernel.project_dir') . '/asso.xlsx';

            // Charger le fichier Excel
            $spreadsheet = IOFactory::load($filePath);

            // Récupérer les données de la première feuille
            $sheet = $spreadsheet->getActiveSheet();

            // Déterminer la dernière colonne non vide
            $highestColumn = $sheet->getHighestColumn();

            // Lire les données de la feuille en excluant les colonnes vides
            $data = array();
            for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
                $rowData = array();
                for ($column = 'A'; $column <= $highestColumn; $column++) {
                    $cell = $sheet->getCell($column . $row);
                    $rowData[] = $cell->getValue();
                }
                $data[] = $rowData;
            }
            // $form = $this->createForm(FileType::class);
            // $form->add('save', SubmitType::class);

            // // ajout fichier excel
            // $form->handleRequest($request);
            // if ($form->isSubmitted() && $form->isValid()) {
            //     /** @var File $file */
            //     $file = $form->getData();

            //     if ($uploadedFile = $form->get('file')->getData()) {
            //         $filename = $uploader->upload($uploadedFile);
            //         $file->setFilename($filename);
            //     }

            //     $em->persist($file);
            //     $em->flush();
            // }
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'data' => $data,
                // 'form' => $form
            ]);
        } catch (ReaderException $e) {
            // Gérer les erreurs de lecture du fichier Excel
            echo 'Erreur de lecture du fichier Excel : ' . $e->getMessage();
        } catch (\Exception $e) {
            // Gérer les autres erreurs
            echo 'Erreur : ' . $e->getMessage();
        }
    }



    // #[Route('/create', name: 'create')]
    // public function create(EntityManagerInterface $em, Request $request, FileUploader $uploader): Response
    // {
    //     $form = $this->createForm(FileType::class);
    //     $form->add('save', SubmitType::class);

    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         /** @var File $file */
    //         $file = $form->getData();

    //         if ($uploadedFile = $form->get('file')->getData()) {
    //             $filename = $uploader->upload($uploadedFile);
    //             $file->setFilename($filename);
    //         }

    //         $em->persist($file);
    //         $em->flush();
    //     }

    //     return $this->render('pages/file/edit.html.twig', [
    //         'form' => $form
    //     ]);
    // }

    // #[Route('/display-image/{filename}', name: 'display_image')]
    // public function displayImage(string $filename = null)
    // {
    //     //        if (!$this->getUser()->getRoles()) {
    //     //            return new Response();
    //     //        }

    //     if ($filename === null) {
    //         return new Response();
    //     }

    //     $image = $this->getParameter('upload_directory') . '/' . $filename;

    //     if (!file_exists($image)) {
    //         return new Response();
    //     }
    //     //        test later is it's not an image do not display
    //     //        if (!in_array($this->getMimeType($image), ['jpeg', 'png', 'svg'])) {
    //     //            return new Response();
    //     //        }

    //     header("Content-type: " . $this->getMimeType($image));
    //     header("Content-Disposition: inline; filename=" . basename($image));

    //     readfile($image);

    //     return new Response();
    // }

    // private function getMimeType($file): string
    // {
    //     $guesser = new MimeTypes();
    //     return $guesser->guessMimeType($file);
    // }
}
