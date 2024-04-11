<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'file')]
class File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $filename;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $import_at = null;

    #[ORM\Column]
    private ?bool $validate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $validate_at = null;

    public function __construct()
    {
        $this->setValidate(false);
        $this->setCreatedAt(new \DateTime());
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTime $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
    public function getImportAt(): ?\DateTime
    {
        return $this->import_at;
    }

    public function setImportAt(?\DateTime $import_at): self
    {
        $this->import_at = $import_at;
        return $this;
    }

    public function isValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): static
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * Get the value of validate_at
     */
    public function getValidate_at(): ?\DateTime
    {
        return $this->validate_at;
    }

    /**
     * Set the value of validate_at
     *
     * @return  self
     */
    public function setValidate_at(?\DateTime $validate_at): self
    {
        $this->validate_at = $validate_at;

        return $this;
    }
}
