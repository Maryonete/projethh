<?php

namespace App\Entity;

use App\Entity\Association;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReferentRepository;

#[ORM\Entity(repositoryClass: ReferentRepository::class)]
class Referent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(targetEntity: "App\Entity\Association", mappedBy: "referent", cascade: ['persist', 'remove'])]
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
    public function setAssociation(Association $association)
    {
        $this->association = $association;
        #TODO : enleve ancien referent

        return $this;
    }
}
