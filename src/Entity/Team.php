<?php

namespace App\Entity;

use App\Entity\Traits\BlameableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedTeams')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $responsible;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams')]
    private Collection $members;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'teams')]
    private ?Team $parentTeam;

    #[ORM\OneToMany(mappedBy: 'parentTeam', targetEntity: self::class)]
    private Collection $teams;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Project::class, orphanRemoval: true)]
    private Collection $projects;

    #[ORM\OneToMany(mappedBy: 'clientTeam', targetEntity: Project::class, orphanRemoval: false)]
    private Collection $clientProjects;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->teams = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->clientProjects = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
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

    public function getResponsible(): ?User
    {
        return $this->responsible;
    }

    public function setResponsible(?User $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    public function getParentTeam(): ?self
    {
        return $this->parentTeam;
    }

    public function setParentTeam(?self $parentTeam): self
    {
        $this->parentTeam = $parentTeam;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(self $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->setParentTeam($this);
        }

        return $this;
    }

    public function removeTeam(self $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getParentTeam() === $this) {
                $team->setParentTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setTeam($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getTeam() === $this) {
                $project->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getClientProjects(): Collection
    {
        return $this->clientProjects;
    }

    public function addClientProject(Project $clientProject): self
    {
        if (!$this->clientProjects->contains($clientProject)) {
            $this->clientProjects[] = $clientProject;
            $clientProject->setTeam($this);
        }

        return $this;
    }

    public function removeClientProject(Project $clientProject): self
    {
        if ($this->clientProjects->removeElement($clientProject)) {
            // set the owning side to null (unless already changed)
            if ($clientProject->getTeam() === $this) {
                $clientProject->setTeam(null);
            }
        }

        return $this;
    }
}
