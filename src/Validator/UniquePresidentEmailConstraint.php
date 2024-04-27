<?php

namespace App\Validator\Constraints;

use App\Entity\President;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UniquePresidentEmailConstraint extends Constraint implements ConstraintValidatorInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function initialize(ExecutionContextInterface $context)
    {
        // Optionally, set default values or perform any initialization here
    }


    public function validate($value, Constraint $constraint)
    {
        // Récupérer l'association en cours de mise à jour
        $association = $constraint->context['association'];

        // Récupérer l'identifiant de l'association en cours de mise à jour
        $associationId = $association->getId();

        // Vérifier si l'email existe déjà comme président d'une autre association
        $otherPresident = $this->entityManager->getRepository(President::class)->findOneByEmail($value);

        if ($otherPresident && $otherPresident->getAssociation()->getId() !== $associationId) {
            $this->context->addViolation('Cet email est déjà utilisé comme président d\'une autre association.');
        }
    }
}
