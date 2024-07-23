<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $task;

    protected function setUp(): void
    {
        $this->task = new Task();
    }

    public function testGetId(): void
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testGetEmail(): void
    {
        $user = new User();
        $this->assertNull($user->getEmail());
    }

    public function testSetEmail(): void
    {
        $user = new User();
        $user->setEmail('test@email.com');
        $this->assertEquals('test@email.com', $user->getEmail());
    }

    public function testGetUserIdentifier(): void
    {
        $user = new User();
        $user->setEmail('test@email.com');
        $this->assertEquals('test@email.com', $user->getUserIdentifier());
    }

    public function testGetRoles(): void
    {
        $user = new User();
        $this->assertIsArray($user->getRoles());
    }

    public function testSetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testGetPassword(): void
    {
        $user = new User();
        $user->setPassword('');
        $this->assertEmpty($user->getPassword());
    }

    public function testSetPassword(): void
    {
        $user = new User();
        $user->setPassword('password');
        $this->assertEquals('password', $user->getPassword());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $user->setPassword('password');
        $this->assertNull($user->eraseCredentials());
    }

    public function testGetUsername(): void
    {
        $user = new User();
        $this->assertNull($user->getUsername());
    }

    public function testSetUsername(): void
    {
        $user = new User();
        $user->setUsername('username');
        $this->assertEquals('username', $user->getUsername());
    }

    public function testGetTasks(): void
    {
        $user = new User();
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $user->getTasks());
    }

    public function testAddTask(): void
    {
        $user = new User();
        $user->addTask($this->createMock(Task::class));
        $this->assertCount(1, $user->getTasks());
    }

    public function testRemoveTask(): void
    {
        $user = new User();
        $task = $this->task;

        $user->addTask($task);
        $user->removeTask($task);

        $this->assertCount(0, $user->getTasks());
        $this->assertNull($task->getAuthor());
    }
}
