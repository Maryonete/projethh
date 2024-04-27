<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\President;
use App\Entity\Association;
use App\Entity\Referent;
use App\Form\CampainAssociationType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CampainAssociationRepository;
use App\Repository\ReferentRepository;
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
        $form = $this->createForm(CampainAssociationType::class, $campAsso);
        $form->add('presidentIsReferent', CheckboxType::class, [
            'label' => 'Le président(e) est également le référent(e)',
            'mapped' => false, // Ce champ n'est pas lié à une propriété de l'entité
            'required' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $request->request->all();
            dump($requestData);
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
            // Referent
            if ($requestData['campain_association']['presidentIsReferent']) {
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
                $referent->setTel($requestData['campain_association']['association']['referent']['tel']);
                $entityManager->persist($referent);
            }
            $entityManager->persist($campAsso);
            $entityManager->flush();
            return new Response('Contenu texte de la réponse');
        }
        return $this->render('verify_data.html.twig', [
            'campAsso'  =>  $campAsso,
            'form'      => $form,
            'oldCampainAssociations' => $oldCampainAssociations,
        ]);
    }
}
