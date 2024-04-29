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


    public function calculateUpdatedAssociationsCount(): int
    {
        return $this->associationRepository->countAllUpdatedAssociations();
    }
}
