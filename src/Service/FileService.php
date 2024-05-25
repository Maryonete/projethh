<?php

namespace App\Service;

use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use App\Entity\CampainAssociation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\{Association, Campains, President, Referent, User};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private Connection $connection,
        private string $upload_directory,
        private Filesystem $filesystem,
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $encoder,
    ) {
    }

    public function upload(
        UploadedFile $file,
        ?string $directory = null
    ): string {
        $original_filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $original_extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $safe_filename = $this->slugger->slug($original_filename);
        $explode = explode('.', $file->getClientOriginalName());
        $extension = '' !== $original_extension ? $original_extension : end($explode);
        $filename = $safe_filename . '-' . uniqid() . '.' . $extension;

        try {
            $full_dir = $this->upload_directory . ($directory ? '/' . $directory : '');

            if (!$this->filesystem->exists($full_dir)) {
                $this->filesystem->mkdir($full_dir);
            }
            $file->move($full_dir, $filename);
        } catch (\Exception $e) {
            // Renvoyer et gérer les erreurs
        }

        return $filename;
    }


    public function initDBTables(): void
    {
        // Suppression des utilisateurs sauf ceux ayant le rôle ROLE_ADMIN

        $sql = "DELETE FROM App\Entity\User u WHERE u.roles NOT LIKE '%ROLE_ADMIN%'";
        $this->entityManager->createQuery($sql)->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Referent')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\President')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Association')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\History')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Campains')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\CampainAssociation')->execute();
        $this->entityManager->flush();
    }

    public function importDataFromFile(
        String $file,
        String $campaignLabel,
        String $startDate
    ): void {
        // Vider la table avant l'importation des nouvelles données
        $this->entityManager->getConnection()->exec('SET FOREIGN_KEY_CHECKS=0');

        try {
            $this->entityManager->beginTransaction();
            $this->initDBTables();
            $this->getData($file, $campaignLabel, $startDate);
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        $this->entityManager->getConnection()->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public function getData(
        String $file,
        String $campaignLabel,
        $startDate
    ): void {
        // Lire le contenu du fichier
        $data = $this->getDataFile($file);

        // Tableau d'utilisateur par email
        $listeEmailUser = [];

        // Création de la première campagne
        $campain = new Campains();
        $campain->isValid(true);
        $campain->setLibelle($campaignLabel);
        $campain->setEmailFrom($_ENV['EMAIL_DEFAULT']);
        $campain->setDate(new DateTimeImmutable($startDate));
        $this->entityManager->persist($campain);

        // La première ligne du tableau contient les entêtes
        for ($i = 1; $i < count($data); $i++) {
            $assoData = $data[$i];
            // Enlever les espaces
            foreach ($assoData as &$element) {
                $element = $element !== null ? trim($element) : null;
            }
            if ($assoData[1] == null) {
                continue;
            }
            if (array_key_exists($assoData[10], $listeEmailUser)) {
                $user = $listeEmailUser[$assoData[10]];
            } else {
                $user = new User();
                $user->setFirstname($assoData[7]);
                $user->setLastname($assoData[8]);
                $user->setEmail($assoData[10]);
                $listeEmailUser[$assoData[10]] = $user;
                $this->entityManager->persist($user);
            }
            $asso = new Association();
            $president = new President();
            $president->setFonction($assoData[9]);
            $president->setUser($user);
            $president->setAssociation($asso);
            $this->entityManager->persist($president);

            // Référent
            if ($assoData[13]) {
                if (array_key_exists($assoData[13], $listeEmailUser)) {
                    $user = $listeEmailUser[$assoData[13]];
                } else {
                    $user = new User();
                    $user->setFirstname($assoData[11]);
                    $user->setLastname($assoData[12]);
                    $user->setEmail($assoData[13]);
                    $listeEmailUser[$assoData[13]] = $user;
                    $this->entityManager->persist($user);
                }
                $referent = new Referent();
                $referent->setTel($assoData[14]);
                $referent->setUser($user);
                $referent->setAssociation($asso);
                $this->entityManager->persist($referent);
                $asso->setReferent($referent);
            }

            $asso->setCode((int)$assoData[0]);
            $asso->setLibelle($assoData[1]);
            $asso->setAdress($assoData[2]);
            $asso->setCp($assoData[3]);
            $asso->setCity($assoData[4]);
            $asso->setTel(isset($assoData[5]) ? $assoData[5] : "");
            $asso->setEmail($assoData[6]);
            $this->entityManager->persist($asso);

            $campainAssociation = new CampainAssociation();
            $campainAssociation->setCampains($campain);
            $campainAssociation->setAssociation($asso);
            $assoData[15] = html_entity_decode($assoData[15]);
            $campainAssociation->setTextePersonnalise($assoData[15]);
            $campainAssociation->setStatut('finish');
            $this->entityManager->persist($campainAssociation);
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
    public function generateCampainExcel(array $campainAsso)
    {
        // Création d'une instance de la classe Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Ajout des en-têtes de colonnes
        $sheet->setCellValue('A1', 'Code asso');
        $sheet->setCellValue('B1', 'Association');
        $sheet->setCellValue('C1', 'Adresse');
        $sheet->setCellValue('D1', 'CP');
        $sheet->setCellValue('E1', 'Ville');
        $sheet->setCellValue('F1', 'Tél');
        $sheet->setCellValue('G1', 'Email');
        $sheet->setCellValue('H1', 'Prénom du président');
        $sheet->setCellValue('I1', 'Nom du président');
        $sheet->setCellValue('J1', 'Fonction du président');
        $sheet->setCellValue('K1', 'Email du président');
        $sheet->setCellValue('L1', 'Prénom du référent');
        $sheet->setCellValue('M1', 'Nom du Référent');
        $sheet->setCellValue('N1', 'Email du référent');
        $sheet->setCellValue('O1', 'Téléphone du référent');
        $sheet->setCellValue('P1', 'Texte personnalisé');
        // Remplissage des données des associations
        $row = 2;
        $campagneLib = "";
        /** @var CampainAssociation $camp */
        foreach ($campainAsso as $camp) {
            $campagneLib = $camp->getCampains()->getLibelle();
            $sheet->setCellValue('A' . $row, $camp->getAssociation()->getCode());
            $sheet->setCellValue('B' . $row, $camp->getAssociation()->getLibelle());
            $sheet->setCellValue('C' . $row, $camp->getAssociation()->getAdress());
            $sheet->setCellValue('D' . $row, $camp->getAssociation()->getCp());
            $sheet->setCellValue('E' . $row, $camp->getAssociation()->getCity());
            $sheet->setCellValue('F' . $row, $camp->getAssociation()->getTel());
            $sheet->setCellValue('G' . $row, $camp->getAssociation()->getEmail());
            $sheet->setCellValue('H' . $row, $camp->getAssociation()->getPresident()->getUser()->getFirstname());
            $sheet->setCellValue('I' . $row, $camp->getAssociation()->getPresident()->getUser()->getLastname());
            $sheet->setCellValue('J' . $row, $camp->getAssociation()->getPresident()->getFonction());
            $sheet->setCellValue('K' . $row, $camp->getAssociation()->getPresident()->getUser()->getEmail());
            if ($camp->getAssociation()->getReferent()) {
                $sheet->setCellValue('L' . $row, $camp->getAssociation()->getReferent()->getUser()->getFirstname());
                $sheet->setCellValue('M' . $row, $camp->getAssociation()->getReferent()->getUser()->getLastname());
                $sheet->setCellValue('N' . $row, $camp->getAssociation()->getReferent()->getUser()->getEmail());
                $sheet->setCellValue('O' . $row, $camp->getAssociation()->getReferent()->getTel());
            }
            $sheet->setCellValue('P' . $row, $camp->getTextePersonnalise());
            // Ajoutez plus de colonnes si nécessaire avec les autres informations de l'association
            $row++;
        }

        // Création d'un objet Writer pour générer le fichier XLSX
        $writer = new Xlsx($spreadsheet);

        // Définition du chemin du fichier
        $filePath = $this->upload_directory . '/associations_' . date('d_m_Y') . '.xlsx';

        // Enregistrement du fichier XLSX
        $writer->save($filePath);
        return $filePath;
    }
    public function getFileNameDownload(String $campainLib): String
    {
        return $this->slugger->slug($campainLib) . '_' . date('d_m_Y') . '-' . uniqid();
    }
}
