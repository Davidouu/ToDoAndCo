<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create 3 user accounts with password "password"
        $users = [
            'user'      => 'password',
            'user1'     => 'password',
            'user2'     => 'password',
            'user3'     => 'password',
            'anonymous' => 'password',
            'userAdmin' => 'password',
        ];

        foreach ($users as $username => $password) {
            $user = new User();
            $user->setEmail($username . '@example.com');

            if ($username === 'userAdmin') {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
            } else {
                $user->setRoles(['ROLE_USER']);
            }

            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
            $user->setUsername($username);
            $manager->persist($user);
        }

        $manager->flush();

        $task = [
            'task1' => [
                'title'   => 'Task 1',
                'content' => 'Content of task 1',
                'isDone'  => false
            ],
            'task2' => [
                'title'   => 'Task 2',
                'content' => 'Content of task 2',
                'isDone'  => false
            ],
            'task3' => [
                'title'   => 'Task 3',
                'content' => 'Content of task 3',
                'isDone'  => false
            ],
            'task4' => [
                'title'   => 'Task 4',
                'content' => 'Content of task 4',
                'isDone'  => false
            ],
            'task5' => [
                'title'   => 'Task 5',
                'content' => 'Content of task 5',
                'isDone'  => false
            ],
            'task6' => [
                'title'   => 'Task 6',
                'content' => 'Content of task 6',
                'isDone'  => false
            ],
            'task7' => [
                'title'   => 'Task 7',
                'content' => 'Content of task 7',
                'isDone'  => false
            ],
            'task8' => [
                'title'   => 'Task 8',
                'content' => 'Content of task 8',
                'isDone'  => false
            ],
            'task9' => [
                'title'   => 'Task 9',
                'content' => 'Content of task 9',
                'isDone'  => false
            ],
            'task10' => [
                'title'   => 'Task 10',
                'content' => 'Content of task 10',
                'isDone'  => false
            ],
            'task11' => [
                'title'   => 'Task 11',
                'content' => 'Content of task 11',
                'isDone'  => false
            ],
            'task12' => [
                'title'   => 'Task 12',
                'content' => 'Content of task 12',
                'isDone'  => false
            ],
            'task13' => [
                'title'   => 'Task 13',
                'content' => 'Content of task 13',
                'isDone'  => false
            ],
            'task14' => [
                'title'   => 'Task 14',
                'content' => 'Content of task 14',
                'isDone'  => false
            ],
            'task15' => [
                'title'   => 'Task 15',
                'content' => 'Content of task 15',
                'isDone'  => false
            ],
        ];

        // Find all
        $test = $manager->getRepository(User::class)->findAll();

        foreach ($task as $taskName => $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setCreatedAt(new \DateTime());

            if (
                $taskName === 'task1' ||
                $taskName === 'task2' ||
                $taskName === 'task3' ||
                $taskName === 'task4' ||
                $taskName === 'task5'
            ) {
                $user = $manager->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
                $task->setAuthor($user);
            } else {
                $user = $manager->getRepository(User::class)->findOneBy(['username' => 'user' . random_int(1, 3)]);
                $task->setAuthor($user);
            }

            $task->setContent($taskData['content']);
            $task->isDone($taskData['isDone']);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
