<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\Probability;
use App\Enum\Severity;
use App\Repository\RiskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RiskRepository::class)]
class Risk
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $identifiedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $resolvedAt;

    #[ORM\Column(type: 'string', length: 255, enumType: Severity::class)]
    private ?Severity $severity;

    #[ORM\Column(type: 'string', length: 255, enumType: Probability::class)]
    private ?Probability $probability;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'risks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Project $project;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdentifiedAt(): ?\DateTimeInterface
    {
        return $this->identifiedAt;
    }

    public function setIdentifiedAt(?\DateTimeInterface $identifiedAt): self
    {
        $this->identifiedAt = $identifiedAt;

        return $this;
    }

    public function getResolvedAt(): ?\DateTimeInterface
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?\DateTimeInterface $resolvedAt): self
    {
        $this->resolvedAt = $resolvedAt;

        return $this;
    }

    public function getSeverity(): ?Severity
    {
        return $this->severity;
    }

    public function setSeverity(Severity $severity): self
    {
        $this->severity = $severity;

        return $this;
    }

    public function getProbability(): ?Probability
    {
        return $this->probability;
    }

    public function setProbability(Probability $probability): self
    {
        $this->probability = $probability;

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
