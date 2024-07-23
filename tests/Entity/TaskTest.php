<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetId(): void
    {
        $task = new Task();
        $this->assertNull($task->getId());
    }

    public function testIsDone(): void
    {
        $task = new Task();
        $this->assertFalse($task->isDone());
    }

    public function testSetDone(): void
    {
        $task = new Task();
        $task->setDone(true);
        $this->assertTrue($task->isDone());
    }

    public function testGetCreatedAt(): void
    {
        $task = new Task();
        $this->assertInstanceOf(\DateTime::class, $task->getCreatedAt());
    }

    public function testSetCreatedAt(): void
    {
        $task = new Task();
        $date = new \DateTime();
        $task->setCreatedAt($date);
        $this->assertEquals($date, $task->getCreatedAt());
    }

    public function testGetTitle(): void
    {
        $task = new Task();
        $this->assertNull($task->getTitle());
    }

    public function testSetTitle(): void
    {
        $task = new Task();
        $task->setTitle('Test Title');
        $this->assertEquals('Test Title', $task->getTitle());
    }

    public function testGetContent(): void
    {
        $task = new Task();
        $this->assertNull($task->getContent());
    }

    public function testSetContent(): void
    {
        $task = new Task();
        $task->setContent('Test Content');
        $this->assertEquals('Test Content', $task->getContent());
    }

    public function testGetAuthor(): void
    {
        $task = new Task();
        $this->assertNull($task->getAuthor());
    }

    public function testSetAuthor(): void
    {
        $task = new Task();
        $task->setAuthor(new User());
        $this->assertInstanceOf(User::class, $task->getAuthor());
    }
}
