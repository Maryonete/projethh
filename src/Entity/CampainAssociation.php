<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CampainAssociationRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CampainAssociationRepository::class)]
#[UniqueEntity(
    fields: ['campains', 'association'],
    message: 'Une seule instance de CampainAssociation est autorisée 
    pour chaque association et campagne.'
)]
class CampainAssociation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTime  $sendAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emails = null;

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

    /**
     * Get the value of emails
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set the value of emails
     *
     * @return  self
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get the value of sendAt
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * Set the value of sendAt
     *
     * @return  self
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }
}
