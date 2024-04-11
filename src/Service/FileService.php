<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\Association;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class FileService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag
    ) {
    }

    public function importDataFromFile(File $file): void
    {
        // Lire le contenu du fichier
        $filePath = $this->parameterBag->get('kernel.project_dir') . '/data/uploads/' . $file->getFilename();
        $data = $this->getDataFile($filePath);

        // la premiere ligne d tableau conteint les entetes
        for ($i = 1; $i < count($data); $i++) {
            $assoData = $data[$i];
            if ($assoData[2] == null) {
                continue;
            }
            $asso = new Association();
            $asso->setCode($assoData[2]);
            $asso->setLibelle($assoData[3]);
            $asso->setAdress($assoData[4]);
            $asso->setCp($assoData[5]);
            $asso->setCity($assoData[6]);
            $asso->setTel(isset($assoData[7]) ? $assoData[7] : "");
            $asso->setEmail($assoData[8]);
            $asso->setDonationCallText($assoData[15]);

            $this->entityManager->persist($asso);
        }

        $this->entityManager->flush();
    }

    /**
     * Retourne un tableau contenant les données du tableau passé
     * en paramètre
     *
     * @param String $path
     * @return array
     */
    public function getDataFile(String $filePath): array
    {
        // Chargement du fichier
        $spreadsheet = IOFactory::load($filePath);

        // Récupérer les données de la première feuille
        $sheet = $spreadsheet->getActiveSheet();

        // Déterminer la dernière colonne non vide
        $highestColumn = 'P';
        // Lire les données de la feuille en excluant les colonnes vides
        $data = array();
        for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
            $rowData = array();
            if (!$sheet->getCell('C' . $row)) {
                continue;
            }
            for ($column = 'A'; $column <= $highestColumn; $column++) {
                $cell = $sheet->getCell($column . $row);

                $rowData[] = $cell->getValue();
            }
            $data[] = $rowData;
        }
        return $data;
    }
}
