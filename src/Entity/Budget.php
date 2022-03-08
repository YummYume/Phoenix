<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\BudgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
class Budget
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'float')]
    private ?float $initialAmount = 0;

    #[ORM\Column(type: 'float')]
    private ?float $spentAmount = 0;

    private float $leftAmount;

    private float $landing;

    #[ORM\OneToOne(mappedBy: 'budget', targetEntity: Project::class, cascade: ['persist', 'remove'])]
    private $project;

    public function __construct()
    {
        $this->leftAmount = $this->initialAmount - $this->spentAmount;
        $this->landing = $this->spentAmount + $this->leftAmount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitialAmount(): ?float
    {
        return $this->initialAmount;
    }

    public function setInitialAmount(float $initialAmount): self
    {
        $this->initialAmount = $initialAmount;

        return $this;
    }

    public function getSpentAmount(): ?float
    {
        return $this->spentAmount;
    }

    public function setSpentAmount(float $spentAmount): self
    {
        $this->spentAmount = $spentAmount;

        return $this;
    }

    public function getLeftAmount(): ?float
    {
        return $this->leftAmount;
    }

    public function setLeftAmount(float $leftAmount): self
    {
        $this->leftAmount = $leftAmount;

        return $this;
    }

    public function getLanding(): ?float
    {
        return $this->landing;
    }

    public function setLanding(float $landing): self
    {
        $this->landing = $landing;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        // set the owning side of the relation if necessary
        if ($project->getBudget() !== $this) {
            $project->setBudget($this);
        }

        $this->project = $project;

        return $this;
    }
}
