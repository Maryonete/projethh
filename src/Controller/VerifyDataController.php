<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Referent;
use App\Entity\President;
use App\Entity\Association;
use App\Form\CampainAssociationType;
use App\Form\CampainAssociation2Type;
use App\Repository\ReferentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CampainAssociationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('', name: 'association')]
class VerifyDataController extends AbstractController
{
    #[Route('/associationdata/{token}', name: 'data')]
    public function verifyData(
        Request $request,
        String $token,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        ReferentRepository $referentRepository,
        CampainAssociationRepository $campAssoRepo

    ): Response {
        $campAsso = $campAssoRepo->findOneBy(['token' => $token]);
        if (!$campAsso) {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
        $oldCampainAssociations = "";
        if ($campAsso && $campAsso->getCampains()->getOldcampain()) {
            $oldCampainAssociations = $campAssoRepo->findOneBy(
                [
                    'campains' => $campAsso->getCampains()->getOldcampain(),
                    'association' => $campAsso->getAssociation()
                ]
            );
        }

        $form = $this->createForm(CampainAssociation2Type::class, $campAsso);
        // Vérifie si l'email du président est le même que celui du référent
        $isPresidentReferent  = $campAsso->getAssociation()->getReferent() !== null
            && $campAsso->getAssociation()->getPresident()->getUser()->getEmail()
            ==
            $campAsso->getAssociation()->getReferent()->getUser()->getEmail();
        $form->add('presidentIsReferent', CheckboxType::class, [
            'label' => 'Le président(e) est également le référent(e)',
            'mapped' => false, // Ce champ n'est pas lié à une propriété de l'entité
            'required' => false,
            'data' => ($isPresidentReferent) ? true : false,
        ]);
        $requestData = $request->request->all();
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            // Récupérer les erreurs de validation
            $errors = $form->getErrors(true);

            // Afficher les erreurs
            foreach ($errors as $error) {
                $this->addFlash(
                    'warning',
                    $error->getMessage()
                );
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if (isset($requestData['president_new'])) {
                $presidentData = $requestData['president_new'];
                // Vérifier si l'email existe déjà comme président d'une autre association
                /* @var President $otherPresident */
                $otherPresident = $entityManager->getRepository(President::class)
                    ->findOneByEmail($presidentData['user']['email']);

                if (
                    $otherPresident
                    && $otherPresident->getAssociation()->getId() !== $campAsso->getAssociation()->getId()
                ) {
                    $this->addFlash(
                        'warning',
                        'Une président d\'une autre association existe déjà avec cette adresse e-mail'
                    );
                    return $this->redirectToRoute(
                        'associationdata',
                        ['token' => $token],
                        Response::HTTP_SEE_OTHER
                    );
                }
                $user = new User();
                $user->setFirstname($presidentData['user']['firstname']);
                $user->setLastname($presidentData['user']['lastname']);
                $user->setEmail($presidentData['user']['email']);

                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        'tttttttt'
                    )
                );
                $entityManager->persist($user);
                $president = new President();
                $president->setUser($user);
                $president->setFonction($presidentData['fonction']);
                $entityManager->persist($president);
                // Ajout du président à l'association
                $campAsso->getAssociation()->setPresident($president);
            }

            if (isset($requestData['campain_association2']['presidentIsReferent'])) {
                dump('presidentIsReferent');
                // on verifie si il existe dejà un referent avec ce mail
                $referent = $referentRepository->findOneBy(
                    ['user' => $campAsso->getAssociation()->getPresident()->getUser()]
                );
                if ($referent) {
                    $campAsso->getAssociation()->setReferent($referent);
                } else {
                    $referent = (new Referent())
                        ->setUser($campAsso->getAssociation()->getPresident()->getUser());
                }
                $campAsso->getAssociation()->setReferent($referent);
                $entityManager->persist($referent);
            }
            // Nouveau referent: on verifie qu'il n'existe pas déjà
            elseif (isset($requestData['referent_new'])) {
                $newRef = $requestData['referent_new'];

                $referent = $referentRepository->findOneBy(
                    ['user' => $newRef['user']['email']]
                );
                if (!$referent) {
                    dump('!referent');
                    $user = new User();
                    $user->setFirstname($newRef['user']['firstname']);
                    $user->setLastname($newRef['user']['lastname']);
                    $user->setEmail($newRef['user']['email']);
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            'tttttttt'
                        )
                    );
                    $entityManager->persist($user);
                    $referent = (new Referent())
                        ->setUser($user);
                }
                $campAsso->getAssociation()->setReferent($referent);
                $referent->setTel($newRef['tel']);
                $entityManager->persist($referent);
            }

            $entityManager->persist($campAsso);
            $entityManager->flush();
            return new Response('<h1>Merci ma princesse belle !!! <3<3<3<3<3<3<3</h1>');
        }
        return $this->render('verify2_data.html.twig', [
            'campAsso'              =>  $campAsso,
            'form'                  => $form,
            'oldCampainAssociations' => $oldCampainAssociations,
            'isPresidentReferent'   =>  $isPresidentReferent,
        ]);
    }
    // #[Route('/associationdata/{token}', name: 'data')]
    // public function verifyData(
    //     Request $request,
    //     String $token,
    //     EntityManagerInterface $entityManager,
    //     UserPasswordHasherInterface $userPasswordHasher,
    //     ReferentRepository $referentRepository,
    //     CampainAssociationRepository $campAssoRepo

    // ): Response {
    //     $campAsso = $campAssoRepo->findOneBy(['token' => $token]);
    //     if (!$campAsso) {
    //         return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    //     }

    //     $oldCampainAssociations = "";
    //     if ($campAsso && $campAsso->getCampains()->getOldcampain()) {
    //         $oldCampainAssociations = $campAssoRepo->findOneBy(
    //             [
    //                 'campains' => $campAsso->getCampains()->getOldcampain(),
    //                 'association' => $campAsso->getAssociation()
    //             ]
    //         );
    //     }
    //     $form = $this->createForm(CampainAssociationType::class, $campAsso);
    //     $form->add('presidentIsReferent', CheckboxType::class, [
    //         'label' => 'Le président(e) est également le référent(e)',
    //         'mapped' => false, // Ce champ n'est pas lié à une propriété de l'entité
    //         'required' => false,
    //     ]);
    //     $requestData = $request->request->all();
    //     dump($requestData);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && !$form->isValid()) {
    //         // Récupérer les erreurs de validation
    //         $errors = $form->getErrors(true);

    //         // Afficher les erreurs
    //         foreach ($errors as $error) {
    //             echo $error->getMessage() . '<br>';
    //         }
    //     }

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $requestData = $request->request->all();
    //         dump($requestData);
    //         if (isset($requestData['president_new'])) {
    //             $presidentData = $requestData['president_new'];
    //             // Vérifier si l'email existe déjà comme président d'une autre association
    //             /* @var President $otherPresident */
    //             $otherPresident = $entityManager->getRepository(President::class)
    //                 ->findOneByEmail($presidentData['user']['email']);

    //             if (
    //                 $otherPresident
    //                 && $otherPresident->getAssociation()->getId() !== $campAsso->getAssociation()->getId()
    //             ) {
    //                 $this->addFlash(
    //                     'warning',
    //                     'Une président d\'une autre association existe déjà avec cette adresse e-mail'
    //                 );
    //                 return $this->redirectToRoute(
    //                     'associationdata',
    //                     ['token' => $token],
    //                     Response::HTTP_SEE_OTHER
    //                 );
    //             }
    //             $user = new User();
    //             $user->setFirstname($presidentData['user']['firstname']);
    //             $user->setLastname($presidentData['user']['lastname']);
    //             $user->setEmail($presidentData['user']['email']);

    //             $user->setPassword(
    //                 $userPasswordHasher->hashPassword(
    //                     $user,
    //                     'tttttttt'
    //                 )
    //             );
    //             $entityManager->persist($user);
    //             $president = new President();
    //             $president->setUser($user);
    //             $president->setFonction($presidentData['fonction']);
    //             $entityManager->persist($president);
    //             // Ajout du président à l'association
    //             $campAsso->getAssociation()->setPresident($president);
    //         }
    //         // Referent
    //         if (isset($requestData['campain_association']['presidentIsReferent'])) {
    //             // on verifie si il existe dejà un referent avec ce mail
    //             $referent = $referentRepository->findOneBy(
    //                 ['user' => $campAsso->getAssociation()->getPresident()->getUser()]
    //             );
    //             if ($referent) {
    //                 $campAsso->getAssociation()->setReferent($referent);
    //             } else {
    //                 $referent = (new Referent())
    //                     ->setUser($campAsso->getAssociation()->getPresident()->getUser());
    //             }
    //             $referent->setTel($requestData['campain_association']['association']['referent']['tel']);
    //             $entityManager->persist($referent);
    //         }
    //         $entityManager->persist($campAsso);
    //         $entityManager->flush();
    //         return new Response('Contenu texte de la réponse');
    //     }
    //     return $this->render('verify_data.html.twig', [
    //         'campAsso'  =>  $campAsso,
    //         'form'      => $form,
    //         'oldCampainAssociations' => $oldCampainAssociations,
    //     ]);
    // }
}