<?php

namespace App\Entity;

use App\Repository\CampainAssociationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampainAssociationRepository::class)]
class CampainAssociation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;



    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texte_personnalise = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedTextAt = null;

    #[ORM\ManyToOne(inversedBy: 'campainAssociations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campains $campains = null;

    #[ORM\ManyToOne(inversedBy: 'campainAssociations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Association $association = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }


    public function getTextePersonnalise(): ?string
    {
        return $this->texte_personnalise;
    }

    public function setTextePersonnalise(?string $texte_personnalise): static
    {
        $this->texte_personnalise = $texte_personnalise;

        return $this;
    }

    public function getUpdatedTextAt(): ?\DateTimeImmutable
    {
        return $this->updatedTextAt;
    }

    public function setUpdatedTextAt(?\DateTimeImmutable $updatedTextAt): static
    {
        $this->updatedTextAt = $updatedTextAt;

        return $this;
    }

    public function getCampains(): ?Campains
    {
        return $this->campains;
    }

    public function setCampains(?Campains $campains): static
    {
        $this->campains = $campains;

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): static
    {
        $this->association = $association;

        return $this;
    }
}
