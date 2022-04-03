<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\BudgetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\NotBlank(allowNull: false, message: 'budget.amount.not_blank')]
    #[Assert\Type(type: 'float', message: 'budget.amount.type')]
    private ?float $initialAmount = 0;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(allowNull: false, message: 'budget.spent_amount.not_blank')]
    #[Assert\Type(type: 'float', message: 'budget.spentAmount.type')]
    #[Assert\PositiveOrZero(message: 'budget.spent_amount.positive_or_zero')]
    private ?float $spentAmount = 0;

    #[ORM\OneToOne(mappedBy: 'budget', targetEntity: Project::class, cascade: ['persist', 'remove'])]
    private ?Project $project;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInitialAmount(): ?float
    {
        return $this->initialAmount;
    }

    public function setInitialAmount(?float $initialAmount): self
    {
        $this->initialAmount = $initialAmount;

        return $this;
    }

    public function getSpentAmount(): ?float
    {
        return $this->spentAmount;
    }

    public function setSpentAmount(?float $spentAmount): self
    {
        $this->spentAmount = $spentAmount;

        return $this;
    }

    public function getLeftAmount(): ?float
    {
        return $this->initialAmount - $this->spentAmount;
    }

    public function getLanding(): ?float
    {
        return $this->getSpentAmount() + $this->getLeftAmount();
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        // set the owning side of the relation if necessary
        if ($project->getBudget() !== $this) {
            $project->setBudget($this);
        }

        $this->project = $project;

        return $this;
    }
}
