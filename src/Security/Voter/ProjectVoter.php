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

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW]) && $subject instanceof Project;
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
        }

        return false;
    }

    private function canEdit(Project $project, User $user): bool
    {
        return $this->security->isGranted(Role::Admin, $user) || $project->getTeam()->getResponsible() === $user;
    }

    private function canView(Project $project, User $user): bool
    {
        if ($this->security->isGranted(Role::Admin, $user)) {
            return true;
        }

        if ($project->isPrivate()) {
            return
                $project->getTeam()->getMembers()->contains($user)
                || $project->getClientTeam()->getMembers()->contains($user)
                || $project->getTeam()->getResponsible() === $user
            ;
        }

        return true;
    }
}
