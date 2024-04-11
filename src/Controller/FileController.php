<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\File;
use App\File\FileUploader;
use App\Form\FileType;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use App\Service\MessageGenerator;
use App\Service\FileService;

#[Route('/file_', name: 'file_')]
class FileController extends AbstractController
{
    /**
     * import file
     *
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param FileUploader $uploader
     * @return Response
     */
    #[Route('new', name: 'new')]
    public function new_file(
        EntityManagerInterface $em,
        Request $request,
        FileUploader $uploader,
        MessageGenerator $messageGenerator
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
            $message = $messageGenerator->getHappyMessage();
            $this->addFlash('success', $message);
            // Récupérer l'ID du fichier nouvellement créé
            $fileId = $file->getId();

            return $this->redirectToRoute('admin_verif_file', ['id' => $fileId], Response::HTTP_SEE_OTHER);
        }
        $files = $em->getRepository(File::class)->findAll();
        return $this->render('admin/files/import.html.twig', [
            'form' => $form,
            'files' => $files
        ]);
    }
    /**
     * Liste les fichiers déposés
     *
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('list', name: 'list')]
    public function list_file(
        EntityManagerInterface $em
    ): Response {
        $files = $em->getRepository(File::class)->findAll();
        return $this->render('admin/files/list.html.twig', [
            'files' => $files
        ]);
    }
    /**
     * Affiche les données contenues dans le fichier importé
     * possibilité de valider le fichier
     */
    #[Route('verif/{id<\d+>}', name: 'verif')]
    public function verif_file(
        FileRepository $fileRepo,
        int $id,
        FileService $fileService
    ): Response {
        $file = $fileRepo->find($id);

        // Vérifier si le fichier existe
        if (!$file) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }
        $data = $fileService->getDataFile($file->getFilename());

        return $this->render('admin/files/verif.html.twig', [
            'data' => $data,
            'file' => $file
        ]);
    }
    /**
     * Fichier importé ok
     *
     */
    #[Route('ok/{id<\d+>}', name: 'ok')]
    public function file_ok(
        EntityManagerInterface $em,
        FileRepository $fileRepo,
        int $id
    ): Response {
        /** @var File $file */
        $file = $fileRepo->find($id);

        // Vérifier si le fichier existe
        if (!$file) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }
        $file->setValidate(true);
        $file->setValidate_at(new \DateTime());
        $em->persist($file);
        $em->flush();

        return $this->redirectToRoute('file_list');
    }
    #[Route('import/{id<\d+>}', name: 'import')]
    public function file_import(
        EntityManagerInterface $em,
        FileRepository $fileRepo,
        int $id,
        FileService $fileService
    ): Response {
        /** @var File $file */
        $file = $fileRepo->find($id);

        // Vérifier si le fichier existe
        if (!$file) {
            throw $this->createNotFoundException('Fichier non trouvé');
        }
        $fileService->importDataFromFile($file);

        // $file->setValidate(true);
        // $file->setValidate_at(new \DateTime());
        // $em->persist($file);
        // $em->flush();

        return $this->redirectToRoute('file_list');
    }
}
