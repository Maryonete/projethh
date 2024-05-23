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

    public function calculateSentEmailsCount(int $campainId): int
    {
        return $this->campainAssociationRepository->countSentEmailsForCampain($campainId);
    }
    public function calculateNbAssoValidateFormCount(int $campainId): int
    {
        return $this->campainAssociationRepository->countNbAssoValidateFormCount($campainId);
    }
    public function calculateNbAssoDeclinedFormCount(int $campainId): int
    {
        return $this->campainAssociationRepository->countNbAssoDeclinedFormCount($campainId);
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
            'nbAssoEnAttenteValidateForm' => 0
        ];

        if ($campain) {
            // Calcul des statistiques spécifiques à la campagne
            $stat['nbSentEmailsCount'] = $this->calculateSentEmailsCount($campain->getId());
            $stat['nbAssoValidateFormCount'] = $this->calculateNbAssoValidateFormCount($campain->getId());
            $stat['nbAssoDeclinedFormCount'] = $this->calculateNbAssoDeclinedFormCount($campain->getId());
            $stat['nbAssoReponseCount'] = $stat['nbAssoValidateFormCount'] + $stat['nbAssoDeclinedFormCount'];

            $stat['percentAssoValidateFormCount'] = ($stat['nbSentEmailsCount'] > 0) ?
                ($stat['nbAssoReponseCount'] * 100 / $stat['nbSentEmailsCount']) : 0;

            $stat['nbAssoEnAttenteValidateForm']
                = $stat['nbSentEmailsCount'] -
                $stat['nbAssoValidateFormCount'] -
                $stat['nbAssoDeclinedFormCount'];
        }

        return $stat;
    }
}
