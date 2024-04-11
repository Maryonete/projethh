<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameAndFunction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signatureImg = null;

    /**
     * @var Collection<int, Association>
     */
    #[ORM\OneToMany(targetEntity: Association::class, mappedBy: 'person')]
    private Collection $associations;

    public function __construct()
    {
        $this->associations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameAndFunction(): ?string
    {
        return $this->nameAndFunction;
    }

    public function setNameAndFunction(string $nameAndFunction): static
    {
        $this->nameAndFunction = $nameAndFunction;

        return $this;
    }

    public function getSignatureImg(): ?string
    {
        return $this->signatureImg;
    }

    public function setSignatureImg(string $signatureImg): static
    {
        $this->signatureImg = $signatureImg;

        return $this;
    }

    /**
     * @return Collection<int, Association>
     */
    public function getAssociations(): Collection
    {
        return $this->associations;
    }

    public function addAssociation(Association $association): static
    {
        if (!$this->associations->contains($association)) {
            $this->associations->add($association);
            $association->setPerson($this);
        }

        return $this;
    }

    public function removeAssociation(Association $association): static
    {
        if ($this->associations->removeElement($association)) {
            // set the owning side to null (unless already changed)
            if ($association->getPerson() === $this) {
                $association->setPerson(null);
            }
        }

        return $this;
    }
}
