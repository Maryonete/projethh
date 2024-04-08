<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        try {
            // Chargement du fichier
            $filePath = $this->getParameter('kernel.project_dir') . '/asso.xlsx';
            // Charger le fichier Excel
            $spreadsheet = IOFactory::load($filePath);

            // Récupérer les données de la première feuille
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Récupérer la lettre de la colonne la plus élevée
            $highestColumn = $sheet->getHighestColumn();

            // Convertir la lettre de la colonne en index de colonne
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            // Trouver la dernière colonne non vide dans la première ligne
            for ($i = $highestColumnIndex; $i >= 1; $i--) {
                $cellValue = $sheet->getCellByColumnAndRow($i, 1)->getValue();
                if (!empty($cellValue)) {
                    break;
                }
            }

            // Filtrer les colonnes vides
            $data = array_map(function ($row) use ($i) {
                return array_slice($row, 0, $i);
            }, $data);
            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                'data' => $data,
            ]);
        } catch (ReaderException $e) {
            // Gérer les erreurs de lecture du fichier Excel
            echo 'Erreur de lecture du fichier Excel : ' . $e->getMessage();
        } catch (\Exception $e) {
            // Gérer les autres erreurs
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
