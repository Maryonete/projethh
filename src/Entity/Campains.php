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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_send = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $objet_email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texte_email = null;

    #[ORM\Column(type: "array", length: 255, nullable: true)]
    private ?array  $destinataire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_from = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_cc = null;

    /**
     * @var Collection<int, CampainAssociation>
     */
    #[ORM\OneToMany(targetEntity: CampainAssociation::class, mappedBy: 'campains')]
    private Collection $campainAssociations;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'],  fetch: "EAGER")]
    private ?self $oldcampain = null;

    #[ORM\Column]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->campainAssociations = new ArrayCollection();
        $this->setEmailFrom($_ENV['EMAIL_DEFAULT']);
    }
    public function __toString(): String
    {
        return $this->getLibelle();
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

    public function getDateSend(): ?\DateTimeInterface
    {
        return $this->date_send;
    }

    public function setDateSend(\DateTimeInterface $date_send): static
    {
        $this->date_send = $date_send;

        return $this;
    }


    public function getObjetEmail(): ?string
    {
        return $this->objet_email;
    }

    public function setObjetEmail(string $objet_email): static
    {
        $this->objet_email = $objet_email;

        return $this;
    }

    public function getTexteEmail(): ?string
    {
        return $this->texte_email;
    }

    public function setTexteEmail(string $texte_email): static
    {
        $this->texte_email = $texte_email;

        return $this;
    }

    public function getDestinataire(): ?array
    {
        return $this->destinataire;
    }

    public function setDestinataire(?array $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }


    /**
     * Get the value of email_from
     */
    public function getEmailFrom()
    {
        return $this->email_from;
    }

    /**
     * Set the value of email_from
     *
     * @return  self
     */
    public function setEmailFrom($email_from)
    {
        $this->email_from = $email_from;

        return $this;
    }

    /**
     * Get the value of email_cc
     */
    public function getEmailCc()
    {
        return $this->email_cc;
    }

    /**
     * Set the value of email_cc
     *
     * @return  self
     */
    public function setEmailCc($email_cc)
    {
        $this->email_cc = $email_cc;

        return $this;
    }

    public function getOldcampain(): ?self
    {
        return $this->oldcampain;
    }

    public function setOldcampain(?self $oldcampain): static
    {
        $this->oldcampain = $oldcampain;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): static
    {
        $this->valid = $valid;

        return $this;
    }
}
