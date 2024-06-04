<?php

namespace App\Entity;

use TypeError;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[ORM\UniqueConstraint(
    name: 'UNIQ_IDENTIFIER_EMAIL',
    fields: ['email']
)]
#[ORM\EntityListeners(['App\EntityListener\UserListener'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Length(min: 2, max: 180)]
    #[Assert\Email(
        message: 'Cet email {{ value }} n\'est pas valide',
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank()]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(min: 2, max: 60)]
    private ?string $firstname = null;

    #[ORM\Column(length: 60)]
    #[Assert\Length(min: 2, max: 60)]
    private ?string $lastname = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?President $president = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Referent $referent = null;

    /**
     * @var Collection<int, History>
     */
    #[ORM\OneToMany(targetEntity: History::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $histories;

    public function __construct()
    {
        $this->setPlainPassword('tttttttt');
        $this->histories = new ArrayCollection();
    }
    public function __toString()
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new TypeError('Format de l\'Email Incorrect');
        } else {
            $this->email = $email;
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }



    public function getPresident(): ?President
    {
        return $this->president;
    }

    public function setPresident(?President $president): static
    {
        // Vérifie si le président passé en argument est différent de l'actuel président
        if ($president !== $this->president) {
            // Vérifie si l'actuel président n'est pas null et s'il appartient à cet utilisateur
            if ($this->president !== null && $this->president->getUser() === $this) {
                // Désactive la relation entre cet utilisateur et l'ancien président
                $this->president->setUser(null);
            }

            // Met à jour la relation avec le nouveau président
            $this->president = $president;

            // Vérifie si le nouveau président est différent de null
            //et s'il n'a pas déjà cet utilisateur comme président
            if ($president !== null && $president->getUser() !== $this) {
                // Établit la relation entre le nouveau président et cet utilisateur
                $president->setUser($this);
            }
        }

        return $this;
    }


    /**
     * Get the value of referent
     */
    public function getReferent()
    {
        return $this->referent;
    }

    /**
     * Set the value of referent
     *
     * @return  self
     */
    public function setReferent($referent)
    {
        $this->referent = $referent;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return Collection<int, History>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): static
    {
        if (!$this->histories->contains($history)) {
            $this->histories->add($history);
            $history->setUser($this);
        }

        return $this;
    }

    public function removeHistory(History $history): static
    {
        if ($this->histories->removeElement($history) && $history->getUser() === $this) {
            // set the owning side to null (unless already changed)
            $history->setUser(null);
        }

        return $this;
    }
}
