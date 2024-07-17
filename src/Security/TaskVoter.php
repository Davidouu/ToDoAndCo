<?php

namespace App\Security;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoter extends Voter
{
    public const DELETE = 'delete';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::DELETE) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $task = $subject;

        return match($attribute) {
            self::DELETE => $this->canDelete($task, $user),
            default      => false,
        };
    }

    private function canDelete(Task $task, User $user): bool
    {
        return $user === $task->getAuthor();
    }
}
