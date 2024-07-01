<?php

namespace App\EntityListener;

use App\Entity\Traces;
use App\Entity\Referent;
use App\Entity\President;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(Events::postPersist)]
#[AsDoctrineListener(Events::postUpdate)]
class TracesListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }


    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        // Vérifie si l'entité créée est de type President ou Referent
        if ($entity instanceof President || $entity instanceof Referent) {
            // Obtient l'ancienne instance de President ou Referent
            $oldEntity = null;
            $role = "";
            if ($entity instanceof President) {
                $role = Traces::ROLE_PRESIDENT;
            } elseif ($entity instanceof Referent) {
                $role = Traces::ROLE_REFERENT;
            }
            $oldEntity = $this->entityManager->getRepository(Traces::class)->findOneBy([
                'role'    => $role,
                'user'        =>  $entity->getUser(),
                'endDate'       => null,
            ]);
            // Vérifie s'il existe une ancienne instance
            if ($oldEntity !== null) {
                $oldEntity->setEndDate(new \DateTime());
                $this->entityManager->persist($oldEntity);
                $this->entityManager->flush();
            }
            $association = $entity->getAssociation();
            if ($association) {
                // Crée une nouvelle instance de l'entité Traces
                $traces = new Traces();
                $traces->setRole($role);
                $traces->setStartDate(new \DateTime()); // Enregistrer la date de création
                $traces->setUser($entity->getUser());
                $traces->setAssociation($association);
                // Enregistre l'entité Traces dans la base de données
                $this->entityManager->persist($traces);
                $this->entityManager->flush();
            }
        }
    }


    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        // Vérifie si l'entité créée est de type President ou Referent
        if ($entity instanceof President || $entity instanceof Referent) {

            // Obtient l'entité Association associée à l'entité actuelle
            $association = $entity->getAssociation();
            // Obtient l'ancienne instance de President ou Referent
            $oldEntity = null;
            $role = "";
            if ($entity instanceof President) {
                $role = Traces::ROLE_PRESIDENT;
            } elseif ($entity instanceof Referent) {
                $role = Traces::ROLE_REFERENT;
            }
            $oldEntity = $this->entityManager->getRepository(Traces::class)->findOneBy([
                'association' => $association,
                'role' => $role,
                'endDate' => null
            ]);
            // Vérifie s'il existe une ancienne instance
            if ($oldEntity !== null) {
                $oldEntity->setEndDate(new \DateTime());
                $this->entityManager->persist($oldEntity);
            }
            if ($association) {
                // Crée une nouvelle instance de l'entité Traces
                $traces = new Traces();
                $traces->setRole($role);
                $traces->setStartDate(new \DateTime()); // Enregistrer la date de création
                $traces->setUser($entity->getUser());
                $traces->setAssociation($association);
                // Enregistre l'entité Traces dans la base de données
                $this->entityManager->persist($traces);
                $this->entityManager->flush();
            }
        }
    }
}
