<?php

namespace App\Entity;

use App\Repository\PresidentRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PresidentRepository::class)]
class President
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction = null;

    #[ORM\OneToOne(targetEntity: "App\Entity\Association", mappedBy: "president", cascade: ['persist', 'remove'])]
    private ?Association $association;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __toString()
    {
        return $this->getUser()->getFirstname() . ' ' . $this->getUser()->getLastname();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // // unset the owning side of the relation if necessary
        // if ($user === null && $this->user !== null) {
        //     $this->user->setPresident(null);
        // }

        // // set the owning side of the relation if necessary
        // if ($user !== null && $user->getPresident() !== $this) {
        //     $user->setPresident($this);
        // }

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
    public function setAssociation($association): President
    {
        dump('setAssociation');
        #TODO enleve ancien president + ajout date dans historique
        $this->association = $association;

        if ($association !== null) {
            $association->setPresident($this);
        }

        return $this;
    }
    public function removeAssociation(): President
    {
        #TODO enleve ancien president + ajout date dans historique
        $this->association = null;

        return $this;
    }
}
