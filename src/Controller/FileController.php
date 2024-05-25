<?php

namespace App\Controller;

use App\Entity\Campains;
use App\Repository\CampainAssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Service\FileService;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


use Symfony\Component\Validator\Constraints\NotBlank;

#[Route('/file_', name: 'file_')]
class FileController extends AbstractController
{

    /**
     * import file
     *
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param FileService $uploader
     * @return Response
     */
    #[Route('new', name: 'new')]
    public function new_file(
        Request $request,
        FileService $fileService
    ): Response {

        $form = $this->createFormBuilder(null, ['attr' => ['class' => 'your-form-class']])
            ->add('file', FileType::class, [
                'label' => '<i class="fas fa-file-excel"></i> Fichier XLSX',
                'label_html'    => true, // Permet d'interpréter le HTML dans le label
                'mapped'        => false,
                'required'      => false,
                'constraints'   => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un fichier.',
                    ]),
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier au format XLSX.',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Visualiser et importer le fichier',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($uploadedFile = $form->get('file')->getData()) {
                // Save file in data/uploads
                $filename = $fileService->upload($uploadedFile);

                $data = $fileService->getDataFile($filename);
                return $this->render('admin/files/verif.html.twig', [
                    'data'      => $data,
                    'filepath'  => $filename
                ]);
            }
        }
        return $this->render('admin/files/import.html.twig', [
            'form' => $form,
        ]);
    }
    /**
     * Import data's file in database
     */
    #[Route('import/', name: 'import')]
    public function file_import(
        FileService $fileService,
        Request $request,
        string $upload_directory
    ): Response {
        $filepath = $request->request->get('filepath');
        $campaignLabel = $request->request->get('campaign_label');
        $startDate = $request->request->get('start_date');
        $fileService->importDataFromFile($filepath, $campaignLabel, $startDate);

        return $this->redirectToRoute('asso_home');
    }

    #[Route('upload/{campain<[0-9]+>}', name: 'upload')]
    public function telechargerExcel(
        CampainAssociationRepository $campainAssoRepo,
        FileService $fileService,
        Campains $campain
    ): Response {
        // Récupération des associations depuis votre repository ou service
        $campainAsso = $campainAssoRepo->findBy([
            // 'statut' => 'send',
            'campains' => $campain
        ]);
        $filePath = $fileService->generateCampainExcel($campainAsso);
        $fileName = $fileService->getFileNameDownload($campain->getLibelle());
        // Définir les en-têtes HTTP
        $response = new Response();
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        // Envoyer le contenu du fichier
        $response->setContent(file_get_contents($filePath));

        // Suppression du fichier temporaire après téléchargement
        unlink($filePath);

        return $response;
    }
}
