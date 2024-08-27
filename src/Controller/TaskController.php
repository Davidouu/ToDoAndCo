<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'app_task_list', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $done = $request->query->get('done');

        $criteria = [];
        if ($done === '0') {
            $criteria['isDone'] = false;
        } elseif ($done === '1') {
            $criteria['isDone'] = true;
        }

        $tasks = empty($criteria) ? $em->getRepository(Task::class)->findAll() : $em->getRepository(Task::class)->findBy($criteria);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'done'  => (string) $done,
        ]);
    }

    #[Route('/tasks/create', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été ajoutée.');

            return $this->redirectToRoute('app_task_list', ['done' => false]);
        }

        // En attendant de reporter le code des différentes vues et méthodes
        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $editedTask = new Task();
        $form = $this->createForm(TaskType::class, $editedTask);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setTitle($editedTask->getTitle());
            $task->setContent($editedTask->getContent());

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('app_task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/delete', name: 'app_task_delete', methods: ['GET'])]
    #[IsGranted('delete', 'task')]
    public function delete(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('app_task_list');
    }

    #[Route('/tasks/{id}/toggle', name: 'app_task_toggle', methods: ['GET'])]
    public function toggle(Task $task, EntityManagerInterface $em): Response
    {
        $task->setDone(!$task->isDone());

        $em->persist($task);
        $em->flush();

        $message = $task->isDone() ? 'faite' : 'à faire';
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme %s.', $task->getTitle(), $message));

        return $this->redirectToRoute('app_task_list');
    }
}
