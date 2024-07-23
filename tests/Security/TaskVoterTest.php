<?php

namespace App\Tests\Security;

use App\Entity\Task;
use App\Entity\User;
use App\Security\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TaskVoterTest extends TestCase
{
    private $token;

    private $voter;

    private $subject;

    private $tokenNull;

    public function setUp(): void
    {
        $user = new User();

        $this->voter = new TaskVoter();
        $this->subject = new Task();

        $this->token = $this->createMock(TokenInterface::class);
        $this->token
            ->method('getUser')
            ->willReturn($user);

        $this->tokenNull = $this->createMock(TokenInterface::class);
        $this->tokenNull
            ->method('getUser')
            ->willReturn(null);
    }

    public function testVoteWithAnotheruserDeleting(): void
    {
        $this->subject->setAuthor(new User());
        $attributes = TaskVoter::DELETE;
        $expectedVote = TaskVoter::ACCESS_DENIED;

        $this->assertEquals($expectedVote, $this->voter->vote($this->token, $this->subject, [$attributes]));
    }

    public function testVoteWithOwnerDeleting(): void
    {
        $this->subject->setAuthor($this->token->getUser());
        $attributes = TaskVoter::DELETE;
        $expectedVote = TaskVoter::ACCESS_GRANTED;

        $this->assertEquals($expectedVote, $this->voter->vote($this->token, $this->subject, [$attributes]));
    }

    public function testVoteWithAdminDeletingAnonTask(): void
    {
        $this->token->getUser()->setRoles(['ROLE_ADMIN']);
        $this->subject->setAuthor(new User());
        $this->subject->getAuthor()->setUsername('anonymous');
        $attributes = TaskVoter::DELETE;
        $expectedVote = TaskVoter::ACCESS_GRANTED;

        $this->assertEquals($expectedVote, $this->voter->vote($this->token, $this->subject, [$attributes]));
    }

    public function testVoteWithAAttrDiffFromDelete(): void
    {
        $attributes = 'edit';
        $expectedVote = TaskVoter::ACCESS_ABSTAIN;

        $this->assertEquals($expectedVote, $this->voter->vote($this->token, $this->subject, [$attributes]));
    }

    public function testVoteWithSubjectDiffFromTask(): void
    {
        $attributes = TaskVoter::DELETE;
        $expectedVote = TaskVoter::ACCESS_ABSTAIN;

        $this->assertEquals($expectedVote, $this->voter->vote($this->token, new User(), [$attributes]));
    }

    public function testVoteWhenUserIsNotAnInstanceOfUser(): void
    {
        $attributes = TaskVoter::DELETE;
        $expectedVote = TaskVoter::ACCESS_DENIED;

        $this->assertEquals($expectedVote, $this->voter->vote($this->tokenNull, $this->subject, [$attributes]));
    }
}
