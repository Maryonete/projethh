<?php

namespace App\Service;

use App\Repository\EmailRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use App\Repository\CampainAssociationRepository;

class StatsCalculator
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private AssociationRepository $associationRepository,
        private CampainAssociationRepository $campainAssociationRepository
    ) {
    }
    public function calculateNbAssoCount(): int
    {
        return $this->associationRepository->countAssociations();
    }


    public function calculateUpdatedAssociationsCount(): int
    {
        return $this->associationRepository->countAllUpdatedAssociations();
    }
    public function calculateStats($campain, $campainAssoRepo)
    {
        $stat = [
            'nbAssoCount'               => $this->calculateNbAssoCount(),
            'nbSentEmailsCount'         => 0,
            'nbAssoValidateFormCount'   => 0,
            'nbAssoDeclinedFormCount'   => 0,
            'nbAssoReponseCount'        => 0,
            'percentAssoValidateFormCount' => 0,
            'nbAssoEnAttenteValidateForm' => 0,
            'calculateNbAssoFinishedCount' => 0,
        ];

        if ($campain) {
            $counts = $this->campainAssociationRepository->getCountsForCampain($campain->getId());

            // Calcul des statistiques spécifiques à la campagne
            $stat['nbSentEmailsCount'] = $counts['sentEmailsCount'];
            $stat['nbAssoFinishedCount'] = $counts['finishedCount'];
            $stat['nbAssoValidateFormCount'] = $counts['updatedCount'];
            $stat['nbAssoDeclinedFormCount'] = $counts['declinedCount'];
            $stat['nbAssoReponseCount'] = $stat['nbAssoValidateFormCount'] + $stat['nbAssoDeclinedFormCount'];

            $stat['percentAssoValidateFormCount'] = ($stat['nbSentEmailsCount'] > 0) ?
                ($stat['nbAssoReponseCount'] * 100 / $stat['nbSentEmailsCount']) : 0;

            $stat['nbAssoEnAttenteValidateForm']
                = $stat['nbSentEmailsCount'] -
                $stat['nbAssoValidateFormCount'] -
                $stat['nbAssoDeclinedFormCount'] -
                $stat['calculateNbAssoFinishedCount'];
        }

        return $stat;
    }
}
