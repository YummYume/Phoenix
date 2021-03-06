<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(allowNull: false, message: 'event.date.not_blank')]
    #[Assert\Type(type: 'datetime', message: 'event.date.type')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'project.startAt', message: 'event.date.greater_than_or_equal')]
    private ?\DateTime $date;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(allowNull: false, message: 'event.name.not_blank')]
    private ?string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: Milestone::class, inversedBy: 'events')]
    #[Assert\NotBlank(allowNull: true, message: 'event.milestone.not_blank')]
    #[Assert\Valid()]
    private ?Milestone $milestone;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(allowNull: false, message: 'event.project.not_blank')]
    #[Assert\Valid()]
    private ?Project $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMilestone(): ?Milestone
    {
        return $this->milestone;
    }

    public function setMilestone(?Milestone $milestone): self
    {
        $this->milestone = $milestone;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
