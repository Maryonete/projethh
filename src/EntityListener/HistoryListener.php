<?php

namespace App\EntityListener;

use App\Entity\History;
use App\Entity\Referent;
use App\Entity\President;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;

#[AsDoctrineListener(Events::postPersist)]
#[AsDoctrineListener(Events::postUpdate)]
class HistoryListener
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
                $role = History::ROLE_PRESIDENT;
            } elseif ($entity instanceof Referent) {
                $role = History::ROLE_REFERENT;
            }
            $oldEntity = $this->entityManager->getRepository(History::class)->findOneBy([
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
                // Crée une nouvelle instance de l'entité History
                $history = new History();
                $history->setRole($role);
                $history->setStartDate(new \DateTime()); // Enregistrer la date de création
                $history->setUser($entity->getUser());
                $history->setAssociation($association);
                // Enregistre l'entité History dans la base de données
                $this->entityManager->persist($history);
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
                $role = History::ROLE_PRESIDENT;
            } elseif ($entity instanceof Referent) {
                $role = History::ROLE_REFERENT;
            }
            $oldEntity = $this->entityManager->getRepository(History::class)->findOneBy([
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
                // Crée une nouvelle instance de l'entité History
                $history = new History();
                $history->setRole($role);
                $history->setStartDate(new \DateTime()); // Enregistrer la date de création
                $history->setUser($entity->getUser());
                $history->setAssociation($association);
                // Enregistre l'entité History dans la base de données
                $this->entityManager->persist($history);
                $this->entityManager->flush();
            }
        }
    }
}
