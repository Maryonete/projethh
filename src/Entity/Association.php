<?php

namespace App\Entity;

use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AssociationRepository::class)]
class Association
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    private ?string $cp = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'association', cascade: ['persist', 'remove'])]
    private ?President $president = null;

    #[ORM\OneToOne(mappedBy: 'association', cascade: ['persist', 'remove'])]
    private ?Referent $referent = null;
    /**
     * @var Collection<int, CampainAssociation>
     */
    #[ORM\OneToMany(targetEntity: CampainAssociation::class, mappedBy: 'association')]
    private Collection $campainAssociations;

    /**
     * @var Collection<int, Traces>
     */
    #[ORM\OneToMany(targetEntity: Traces::class, mappedBy: 'association', orphanRemoval: true)]
    private Collection $traces;

    // Par défaut, l'association est active
    #[ORM\Column(length: 255)]
    private ?string $status = 'active';

    public function __construct()
    {
        $this->campainAssociations = new ArrayCollection();
        $this->traces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(string $cp): static
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
            $campainAssociation->setAssociation($this);
        }

        return $this;
    }

    public function removeCampainAssociation(CampainAssociation $campainAssociation): static
    {
        if (
            $this->campainAssociations->removeElement($campainAssociation)
            && $campainAssociation->getAssociation() === $this
        ) {
            $campainAssociation->setAssociation(null);
        }

        return $this;
    }

    public function getPresident(): ?President
    {
        return $this->president;
    }

    public function setPresident(?President $president): static
    {
        if ($this->president !== null && $this->president !== $president) {
            $this->president->setAssociation(null);
        }
        $this->president = $president;
        if ($president !== null) {
            $this->president->setAssociation($this);
        }
        return $this;
    }



    /**
     * @return Collection<int, Traces>
     */
    public function getTraces(): Collection
    {
        return $this->traces;
    }

    public function addTraces(Traces $traces): static
    {
        if (!$this->traces->contains($traces)) {
            $this->traces->add($traces);
            $traces->setAssociation($this);
        }

        return $this;
    }

    public function removeTraces(Traces $traces): static
    {
        if ($this->traces->removeElement($traces) && $traces->getAssociation() === $this) {
            $traces->setAssociation(null);
        }

        return $this;
    }

    /**
     * Get the value of referent
     */
    public function getReferent(): ?Referent
    {
        return $this->referent;
    }

    /**
     * Set the value of referent
     *
     * @return  self
     */
    // Dans votre classe Association
    public function setReferent($referent): self
    {
        if ($this->referent !== null && $this->referent !== $referent) {
            $this->referent->setAssociation(null);
        }

        $this->referent = $referent;
        if ($referent !== null) {
            $referent->setAssociation($this);
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
