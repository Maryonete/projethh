<?php

namespace App\Entity;

use App\Repository\CampainsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampainsRepository::class)]
class Campains
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    /**
     * @var Collection<int, CampainAssociation>
     */
    #[ORM\OneToMany(targetEntity: CampainAssociation::class, mappedBy: 'campains')]
    private Collection $campainAssociations;

    public function __construct()
    {
        $this->campainAssociations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, CampainAssociation>
     */
    public function getCampainAssociations(): Collection
    {
        return $this->campainAssociations;
    }

    public function addCampainAssociation(CampainAssociation $campainAssociation): static
    {
        if (!$this->campainAssociations->contains($campainAssociation)) {
            $this->campainAssociations->add($campainAssociation);
            $campainAssociation->setCampains($this);
        }

        return $this;
    }

    public function removeCampainAssociation(CampainAssociation $campainAssociation): static
    {
        if ($this->campainAssociations->removeElement($campainAssociation)) {
            // set the owning side to null (unless already changed)
            if ($campainAssociation->getCampains() === $this) {
                $campainAssociation->setCampains(null);
            }
        }

        return $this;
    }
}
