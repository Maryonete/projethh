<?php

namespace App\Service;

use App\Entity\{File, Association, Person};
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private Connection $connection,
        private string $upload_directory,
        private Filesystem $filesystem,
        private SluggerInterface $slugger
    ) {
    }
    public function upload(UploadedFile $file, ?string $directory = null): string
    {
        $original_filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $original_extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $safe_filename = $this->slugger->slug($original_filename);
        $explode = explode('.', $file->getClientOriginalName());
        $extension = '' !== $original_extension ? $original_extension : end($explode);
        $filename = $safe_filename . '-' . uniqid() . '.' . $extension;

        try {
            $full_dir = $this->upload_directory . ($directory ? '/' . $directory : '');

            $fs = new Filesystem();

            if (!$fs->exists($full_dir)) {
                $fs->mkdir($full_dir);
            }
            $file->move($full_dir, $filename);
        } catch (\Exception $e) {
            // renvoyer et gérer les erreur
        }

        return $filename;
    }
    public function deleteFile(File $file): bool
    {
        try {
            // Supprimer le fichier
            $this->filesystem->remove($this->upload_directory  . '/' . $file->getFileName());
            $this->entityManager->remove($file);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            // Gérer les erreurs, par exemple, en journalisant l'erreur
            // ou en retournant false pour indiquer que la suppression a échoué
            return false;
        }
    }
    /**
     * Vide la table passée en parametre
     *
     * @param string $tableName
     * @return void
     */
    private function truncateTable(string $tableName): void
    {
        $sql = "TRUNCATE TABLE $tableName";
        $this->connection->executeStatement($sql);
    }
    public function importDataFromFile(File $file): void
    {

        // Lire le contenu du fichier
        $data = $this->getDataFile($file->getFilename());

        // Vider la table avant l'importation des nouvelles données
        // Supprimer les enregistrements de la table "Association"
        $this->entityManager->createQuery('DELETE FROM App\Entity\Association')->execute();

        // Supprimer les enregistrements de la table "Person"
        $this->entityManager->createQuery('DELETE FROM App\Entity\Person')->execute();

        // la premiere ligne d tableau conteint les entetes
        for ($i = 1; $i < count($data); $i++) {
            $assoData = $data[$i];
            if ($assoData[2] == null) {
                continue;
            }
            $person = new Person();
            $person->setNameAndFunction($assoData[12]);
            $this->entityManager->persist($person);

            $asso = new Association();
            $asso->setPerson($person);
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
        dump($this->upload_directory);
        // Chargement du fichier
        $spreadsheet = IOFactory::load($this->upload_directory  . '/' . $filePath);

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

    public function downloadFile(String $filepath): void
    {
        $file = $this->upload_directory  . '/' . $filepath;
        if (file_exists($file)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename=' . $filepath);
            header('Content-Length: ' . filesize($file));
            readfile($file);
        }
    }
}
