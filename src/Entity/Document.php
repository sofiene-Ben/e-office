<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Folder $folder = null;

    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    

    #[ORM\PrePersist]
    public function setSlug()
    {
        $slugger = new AsciiSlugger('fr_FR');

        $this->slug = $slugger->slug(strtolower($this->name)  .'-' . time());
    }

    #[ORM\PrePersist]
    public function setCreatedAt()
    {
       $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt()
    {
        $this->updateAt = new \DateTime();
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): static
    {
        $this->folder = $folder;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

}
