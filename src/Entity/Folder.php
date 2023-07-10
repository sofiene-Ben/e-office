<?php

namespace App\Entity;

use App\Repository\FolderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FolderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Folder
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

    #[ORM\ManyToOne(inversedBy: 'folders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Library $library = null;

    #[ORM\ManyToOne(inversedBy: 'folders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'folder', targetEntity: Document::class, orphanRemoval: true)]
    private Collection $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }



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

    public function getLibrary(): ?Library
    {
        return $this->library;
    }

    public function setLibrary(?Library $library): static
    {
        $this->library = $library;

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

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setFolder($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getFolder() === $this) {
                $document->setFolder(null);
            }
        }

        return $this;
    }


}
