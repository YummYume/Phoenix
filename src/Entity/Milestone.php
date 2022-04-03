<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortableRepository::class)]
class Milestone
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(allowNull: false, message: 'milestone.title.not_blank')]
    private ?string $name;

    #[ORM\Column(type: 'boolean')]
    #[Assert\Type(type: 'bool', message: 'milestone.required.type')]
    private bool $required = true;

    #[Gedmo\SortablePosition]
    #[ORM\Column(type: 'integer')]
    #[Assert\Type(type: 'integer', message: 'milestone.position.type')]
    #[Assert\PositiveOrZero(message: 'milestone.position.positive_or_zero')]
    private ?int $position;

    #[ORM\OneToMany(mappedBy: 'milestone', targetEntity: Event::class)]
    private Collection $events;

    #[Gedmo\SortableGroup]
    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'milestones')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid()]
    private ?Project $project;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank(allowNull: true, message: 'milestone.start_at.not_blank')]
    #[Assert\Type(type: 'datetime', message: 'milestone.start_at.type')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'project.startAt', message: 'milestone.start_at.greater_than_or_equal')]
    private ?\DateTime $startAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\NotBlank(allowNull: true, message: 'milestone.end_at.not_blank')]
    #[Assert\Type(type: 'datetime', message: 'milestone.end_at.type')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'startAt', message: 'milestone.end_at.greater_than_start_at')]
    private ?\DateTime $endAt;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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
            $event->setMilestone($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getMilestone() === $this) {
                $event->setMilestone(null);
            }
        }

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
}
