<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    private UrlGeneratorInterface|null $urlGenerator = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    /**
     *
     * Test list task
     *
     */

    public function testList(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("Liste des tâches")')->count());
        $this->assertSame(1, $crawler->filter('h1')->count());
    }

    public function testListIsDone(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_list', ['done' => 1]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("Liste des tâches terminées")')->count());
        $this->assertSame(1, $crawler->filter('h1')->count());
    }

    public function testListIsNotDone(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_list', ['done' => 0]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("Liste des tâches à faire")')->count());
        $this->assertSame(1, $crawler->filter('h1')->count());
    }

    /**
     *
     * Test add task
     *
     */

    public function testAddTask(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_new'));
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]'   => 'Nouvelle tâche de test',
            'task[content]' => 'Contenu de la tâche de test',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La tâche a bien été ajoutée.');
    }

    public function testAddTaskWithoutLogin(): void
    {
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_new'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }

    public function testAddTaskWithoutTitle(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_new'));
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]'   => '',
            'task[content]' => 'Contenu de la tâche de test titre vide',
        ]);

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertSelectorTextContains('li', 'Vous devez inscrire un titre.');
    }

    public function testAddTaskWithoutContent(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_new'));
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]'   => 'Titre de la tâche de test contenu vide',
            'task[content]' => '',
        ]);

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertSelectorTextContains('li', 'Vous devez inscrire un contenu.');
    }

    /**
     *
     * Test edit task
     *
     */

    public function testEditTask(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->urlGenerator->generate('app_task_edit', ['id' => $taskId])
        );
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]'   => 'Titre de la tâche de test modifiée',
            'task[content]' => 'Contenu de la tâche de test modifiée',
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La tâche a bien été modifiée.');
    }

    public function testEditTaskWithoutLogin(): void
    {
        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();
        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_edit', ['id' => $taskId]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }

    public function testEditTaskWithoutTitle(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->urlGenerator->generate('app_task_edit', ['id' => $taskId])
        );
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]'   => '',
            'task[content]' => 'Contenu de la tâche de test titre vide',
        ]);

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertSelectorTextContains('li', 'Vous devez inscrire un titre.');
    }

    public function testEditTaskWithoutContent(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->urlGenerator->generate('app_task_edit', ['id' => $taskId])
        );
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]'   => 'Titre de la tâche de test contenu vide',
            'task[content]' => '',
        ]);

        $this->client->submit($form);
        $this->client->followRedirects();

        $this->assertSelectorTextContains('li', 'Vous devez inscrire un contenu.');
    }

    /**
     *
     * Test delete task
     *
     */

    public function testDeleteTask(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'userAdmin'])
        );

        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_delete', ['id' => $taskId]));

        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La tâche a bien été supprimée.');
    }

    public function testDeleteTaskWithoutLogin(): void
    {
        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_delete', ['id' => $taskId]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }

    public function testDeleteAnonTaskWithRoleAdmin(): void
    {
        $anonymousUser = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy(['author' => $anonymousUser])->getId();

        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'userAdmin'])
        );

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_delete', ['id' => $taskId]));
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La tâche a bien été supprimée.');
    }

    public function testDeleteAnonTaskWithoutRoleAdmin(): void
    {
        $anonymousUser = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'anonymous']);
        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy(['author' => $anonymousUser])->getId();

        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'user'])
        );

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_delete', ['id' => $taskId]));
        $this->client->followRedirects();

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     *
     * Test toggle task
     *
     */

    public function testToggleTaskToDone(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $task = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy(['isDone' => false]);
        $taskId = $task->getId();
        $taskName = $task->getTitle();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_toggle', ['id' => $taskId]));

        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La tâche ' . $taskName . ' a bien été marquée comme faite.');
    }

    public function testToggleTaskToNotDone(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy([])
        );

        $task = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy(['isDone' => true]);
        $taskId = $task->getId();
        $taskName = $task->getTitle();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_toggle', ['id' => $taskId]));

        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'La tâche ' . $taskName . ' a bien été marquée comme à faire.');
    }

    public function testToggleTaskWithoutLogin(): void
    {
        $taskId = $this->client->getContainer()->get('doctrine')->getRepository(Task::class)->findOneBy([])->getId();

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_toggle', ['id' => $taskId]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }
}
