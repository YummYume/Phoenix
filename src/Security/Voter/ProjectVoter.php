<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\User;
use App\Enum\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProjectVoter extends Voter
{
    public const EDIT = 'PROJECT_EDIT';
    public const VIEW = 'PROJECT_VIEW';
    public const EDIT_MILESTONE = 'PROJECT_EDIT_MILESTONE';
    public const EDIT_RISK = 'PROJECT_EDIT_RISK';
    public const EDIT_EVENT = 'PROJECT_EDIT_EVENT';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::EDIT_MILESTONE, self::EDIT_RISK, self::EDIT_EVENT]) && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
                break;
            case self::VIEW:
                return $this->canView($subject, $user);
                break;
            case self::EDIT_MILESTONE:
                return $this->canEditMilestones($subject, $user);
                break;
            case self::EDIT_RISK:
                return $this->canEditRisks($subject, $user);
                break;
            case self::EDIT_EVENT:
                return $this->canEditEvents($subject, $user);
                break;
        }

        return false;
    }

    private function canEdit(Project $project, User $user): bool
    {
        return $this->security->isGranted(Role::Admin, $user) || $project->getTeam()->getResponsible() === $user;
    }

    private function canView(Project $project, User $user): bool
    {
        if ($project->isPrivate() && !$this->security->isGranted(Role::Admin, $user)) {
            return
                $project->getTeam()->getMembers()->contains($user)
                || $project->getClientTeam()?->getMembers()->contains($user)
                || $project->getClientTeam()?->getResponsible() === $user
                || $project->getTeam()->getParentTeam()?->getMembers()->contains($user)
                || $project->getTeam()->getParentTeam()?->getResponsible() === $user
                || $project->getTeam()->getResponsible() === $user
            ;
        }

        return true;
    }

    private function canEditMilestones(Project $project, User $user): bool
    {
        return $this->canEdit($project, $user) || $project->getTeam()->getMembers()->contains($user);
    }

    private function canEditRisks(Project $project, User $user): bool
    {
        return $this->canEdit($project, $user) || $project->getTeam()->getMembers()->contains($user);
    }

    private function canEditEvents(Project $project, User $user): bool
    {
        return
            $this->canEdit($project, $user)
            || $project->getTeam()->getMembers()->contains($user)
            || $project->getClientTeam()?->getMembers()->contains($user)
            || $project->getClientTeam()?->getResponsible() === $user
        ;
    }
}
