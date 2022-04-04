<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'project.code.unique')]
class Project
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(allowNull: false, message: 'project.name.not_blank')]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: Status::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid()]
    private ?Status $status;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank(allowNull: true, message: 'project.start_at.not_blank')]
    #[Assert\Type(type: 'datetime', message: 'project.start_at.type')]
    private ?\DateTime $startAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank(allowNull: true, message: 'project.end_at.not_blank')]
    #[Assert\Type(type: 'datetime', message: 'project.end_at.type')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startAt', message: 'project.end_at.greater_than_or_equal_start_at')]
    private ?\DateTime $endAt;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $code;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(allowNull: false, message: 'project.team.not_blank')]
    #[Assert\Valid()]
    private ?Team $team;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'clientProjects')]
    #[Assert\NotBlank(allowNull: true, message: 'project.client_team.not_blank')]
    #[Assert\Valid()]
    private ?Team $clientTeam;

    #[ORM\OneToOne(inversedBy: 'project', targetEntity: Budget::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(allowNull: false, message: 'project.budget.not_blank')]
    #[Assert\Valid()]
    private ?Budget $budget;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Event::class, orphanRemoval: true)]
    private Collection $events;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Risk::class, orphanRemoval: true)]
    private Collection $risks;

    #[ORM\Column(type: 'boolean')]
    #[Assert\Type(type: 'bool', message: 'project.archived.type')]
    private ?bool $archived = false;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Milestone::class, orphanRemoval: true)]
    private Collection $milestones;

    #[ORM\Column(type: 'boolean')]
    #[Assert\Type(type: 'bool', message: 'project.private.type')]
    private bool $private = false;

    #[ORM\ManyToMany(targetEntity: Portfolio::class, mappedBy: 'projects')]
    private Collection $portfolios;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->risks = new ArrayCollection();
        $this->milestones = new ArrayCollection();
        $this->portfolios = new ArrayCollection();
    }

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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

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

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getClientTeam(): ?Team
    {
        return $this->clientTeam;
    }

    public function setClientTeam(?Team $clientTeam): self
    {
        $this->clientTeam = $clientTeam;

        return $this;
    }

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(Budget $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setProject($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getProject() === $this) {
                $event->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Risk>
     */
    public function getRisks(): Collection
    {
        return $this->risks;
    }

    public function addRisk(Risk $risk): self
    {
        if (!$this->risks->contains($risk)) {
            $this->risks[] = $risk;
            $risk->setProject($this);
        }

        return $this;
    }

    public function removeRisk(Risk $risk): self
    {
        if ($this->risks->removeElement($risk)) {
            // set the owning side to null (unless already changed)
            if ($risk->getProject() === $this) {
                $risk->setProject(null);
            }
        }

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    /**
     * @return Collection<int, Milestone>
     */
    public function getMilestones(): Collection
    {
        return $this->milestones;
    }

    public function addMilestone(Milestone $milestone): self
    {
        if (!$this->milestones->contains($milestone)) {
            $this->milestones[] = $milestone;
            $milestone->setProject($this);
        }

        return $this;
    }

    public function removeMilestone(Milestone $milestone): self
    {
        if ($this->milestones->removeElement($milestone)) {
            // set the owning side to null (unless already changed)
            if ($milestone->getProject() === $this) {
                $milestone->setProject(null);
            }
        }

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    /**
     * @return Collection<int, Portfolio>
     */
    public function getPortfolios(): Collection
    {
        return $this->portfolios;
    }

    public function addPortfolio(Portfolio $portfolio): self
    {
        if (!$this->portfolios->contains($portfolio)) {
            $this->portfolios[] = $portfolio;
            $portfolio->addProject($this);
        }

        return $this;
    }

    public function removePortfolio(Portfolio $portfolio): self
    {
        if ($this->portfolios->removeElement($portfolio)) {
            $portfolio->removeProject($this);
        }

        return $this;
    }

    public function getBrainData(): array
    {
        $projectData = [
            'startAt' => $this->startAt,
            'endAt' => $this->endAt,
            'archived' => $this->archived,
        ];
        $budgetData = [
            'initialAmount' => $this->budget->getInitialAmount(),
            'spentAmount' => $this->budget->getSpentAmount(),
            'leftAmount' => $this->budget->getLeftAmount(),
        ];
        $riskDatas = [];

        foreach ($this->risks as $risk) {
            $riskDatas[] = [
                'identifiedAt' => $risk->getIdentifiedAt(),
                'resolvedAt' => $risk->getResolvedAt(),
                'probability' => $risk->getProbability()->value,
                'severity' => $risk->getSeverity()->value,
            ];
        }

        return [
            'project' => $projectData,
            'budget' => $budgetData,
            'risks' => $riskDatas,
        ];
    }
}
