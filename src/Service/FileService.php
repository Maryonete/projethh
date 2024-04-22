<?php

namespace App\Service;

use DateTime;
use Doctrine\DBAL\Connection;
use App\Entity\CampainAssociation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Entity\{File, Association, Campains, Person, President, Referent, User};

class FileService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        private Connection $connection,
        private string $upload_directory,
        private Filesystem $filesystem,
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $encoder
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

    /**
     * Vide la table passée en parametre
     *
     * @param string $tableName
     * @return void
     */

    public function importDataFromFile(String $file): void
    {
        // Lire le contenu du fichier
        $data = $this->getDataFile($file);

        // Vider la table avant l'importation des nouvelles données
        $this->entityManager->getConnection()->exec('SET FOREIGN_KEY_CHECKS=0');

        $this->entityManager->beginTransaction();

        try {
            $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Referent')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\President')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Association')->execute();

            $this->entityManager->createQuery('DELETE FROM App\Entity\Campains')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\CampainAssociation')->execute();
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        $this->entityManager->getConnection()->exec('SET FOREIGN_KEY_CHECKS=1'); // Re-enable foreign key checks



        // tableau d'user par email
        $listeEmailUser     = [];

        # #TODO creation admin !!
        $admin = new User();
        $admin->setEmail('test@test.fr');
        $admin->setPassword($this->encoder->hashPassword($admin, 'test'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstname('test');
        $admin->setLastname('test');
        $this->entityManager->persist($admin);

        // création premiere campagne
        $campain = new Campains();
        $campain->isValid(true);
        $campain->setLibelle('Campagne printemps 2024');
        $campain->setEmailFrom($_ENV['EMAIL_DEFAULT']);
        $campain->setDate(DateTime::createFromFormat('Y-m-d', '2024-03-01'));
        $this->entityManager->persist($campain);

        // la premiere ligne d tableau contient les entetes
        for ($i = 1; $i < count($data); $i++) {
            $assoData = $data[$i];
            // enleve espace 
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
                $user->setPassword($this->encoder->hashPassword($user, 'test'));
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
            $this->entityManager->persist($president);
            $asso->setPresident($president);
            // Referent
            if ($assoData[13]) {
                if (array_key_exists($assoData[13], $listeEmailUser)) {
                    $user = $listeEmailUser[$assoData[13]];
                } else {
                    $user = new User();
                    $user->setPassword($this->encoder->hashPassword($user, 'test'));
                    $user->setFirstname($assoData[11]);
                    $user->setLastname($assoData[12]);
                    $user->setEmail($assoData[13]);
                    $listeEmailUser[$assoData[13]] = $user;
                    $this->entityManager->persist($user);
                }
                $referent = new Referent();
                $referent->setTel($assoData[14]);
                $referent->setUser($user);
                $asso->setReferent($referent);
                $this->entityManager->persist($referent);
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
}
