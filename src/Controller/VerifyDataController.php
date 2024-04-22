<?php

namespace App\Controller;

use App\Form\CampainAssociationType;
use App\Repository\AssociationRepository;
use App\Repository\CampainAssociationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VerifyDataController extends AbstractController
{
    #[Route('/associationdata/{token}', name: 'associationdata')]
    public function verifyData(
        Request $request,
        String $token,
        CampainAssociationRepository $campAssoRepo
    ): Response {
        $campAsso = $campAssoRepo->findOneBy(['token' => $token]);

        $oldCampainAssociations = "";
        if ($campAsso->getCampains()->getOldcampain()) {
            $oldCampainAssociations = $campAssoRepo->findOneByCampainsAndAsso(
                $campAsso->getCampains()->getOldcampain(),
                $campAsso->getAssociation()
            );
        }

        $form = $this->createForm(CampainAssociationType::class, $campAsso);
        $form->handleRequest($request);
        return $this->render('verify_data.html.twig', [
            'token'     =>  $token,
            'campAsso'  =>  $campAsso,
            'form' => $form,
            'oldCampainAssociations' => $oldCampainAssociations,
        ]);
    }
}
