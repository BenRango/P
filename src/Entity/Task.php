<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getTasks'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull]
    #[Groups(['getTasks'])]
    private ?string $label = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['getTasks'])]
    private ?bool $state = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getTasks'])]
    #[Assert\NotNull]
    private ?string $creationDate = null;

    #[Groups(['getTasks'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $completionDate = null;

    #[ORM\Column]
    #[Groups(['getTasks'])]
    #[Assert\NotNull]
    private ?bool $modified = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $latestModificationDate = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function isState(): ?bool
    {
        return $this->state;
    }

    public function setState(bool $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCreationDate(): ?string
    {
        return $this->creationDate;
    }

    public function setCreationDate(string $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getCompletionDate(): ?string
    {
        return $this->completionDate;
    }

    public function setCompletionDate(?string $completionDate): static
    {
        $this->completionDate = $completionDate;

        return $this;
    }

    public function isModified(): ?bool
    {
        return $this->modified;
    }

    public function setModified(bool $modified): static
    {
        $this->modified = $modified;

        return $this;
    }

    public function getLatestModificationDate(): ?string
    {
        return $this->latestModificationDate;
    }

    public function setLatestModificationDate(?string $latestModificationDate): static
    {
        $this->latestModificationDate = $latestModificationDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
