<?php

namespace App\Controller;

use App\Repository\PresidentRepository;
use App\Repository\AssociationRepository;
use App\Repository\CampainAssociationRepository;
use App\Repository\CampainsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\StatsCalculator;

#[Route('/asso', name: 'asso_')]
class AdminController extends AbstractController
{
    #[Route('', name: 'home')]
    public function index(
        AssociationRepository $assoRepo,
        CampainsRepository $campainRepo,
        CampainAssociationRepository $campainAssoRepo,
        StatsCalculator $statsCalculator

    ): Response {
        $campain = $campainRepo->findOneByValid(true);
        $campainAssociations = [];
        $oldCampainAssociations = "";

        if ($campain) {
            $campainAssociations = $campainAssoRepo->findByCampains($campain);
            if ($campain->getOldcampain()) {
                $oldCampainAssociations = $campainAssoRepo->findByCampains($campain->getOldcampain());
            }
        }
        $stat = $statsCalculator->calculateStats($campain, $campainAssoRepo);

        return $this->render('admin/index.html.twig', [
            'listAsso'              =>  $assoRepo->findAll(),
            'campain'               =>  $campain,
            'campainAssociations'   =>  $campainAssociations,
            'oldCampainAssociations' => $oldCampainAssociations,
            'stat'                  => $stat,
        ]);
    }
}
