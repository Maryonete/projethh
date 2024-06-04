<?php

namespace App\Entity;

use App\Entity\Association;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReferentRepository;

#[ORM\Entity(repositoryClass: ReferentRepository::class)]
#[ORM\Table(name: 'referent')]
class Referent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\OneToOne(inversedBy: 'referent', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'referent', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'association_id', referencedColumnName: 'id', nullable: true)]
    private ?Association $association = null;



    public function __toString()
    {
        return $this->getUser()->getFirstname() . ' ' . $this->getUser()->getLastname();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
    /**
     * Get the value of association
     */
    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    /**
     * Set the value of association
     *
     * @return  self
     */
    public function setAssociation(?Association $association): static
    {


        // Met à jour la propriété d'association de ce président
        $this->association = $association;

        // Si l'association est différente de null
        // if ($association !== null) {
        //     // Vérifie si cette instance de président est déjà associée à l'association
        //     // if ($association->getReferent() !== $this) {
        //     // Si ce n'est pas le cas, mettez cette instance de président comme président de l'association
        //     $association->setReferent($this);
        //     // }
        // }

        return $this;
    }
}
