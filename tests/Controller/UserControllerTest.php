<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserControllerTest extends WebTestCase
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
     * Test list users
     *
     */

    public function testListUsers(): void
    {
        $this->client->request('GET', $this->urlGenerator->generate('app_user_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }

    public function testListUsersWithUserRole(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'user'])
        );

        $this->client->request('GET', $this->urlGenerator->generate('app_user_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testListUsersWithAdminRole(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_task_list'));

        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'userAdmin'])
        );

        $this->client->request('GET', $this->urlGenerator->generate('app_user_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->followRedirects();

        $this->assertSame(1, $crawler->filter('h1')->count());
    }

    /**
     *
     * Test create user
     *
     */

    public function testCreateUser(): void
    {
        $this->client->request('GET', $this->urlGenerator->generate('app_user_new'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }

    public function testCreateUserWithUserRole(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'user'])
        );

        $this->client->request('GET', $this->urlGenerator->generate('app_user_new'));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateUserWithAdminRole(): void
    {
        $user = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(
            ['username' => 'testCreation']
        );
        if ($user) {
            $this->client->getContainer()->get('doctrine')->getManager()->remove($user);
            $this->client->getContainer()->get('doctrine')->getManager()->flush();
        }

        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'userAdmin'])
        );

        $this->client->request('GET', $this->urlGenerator->generate('app_user_new'));
        $form = $this->client->getCrawler()->selectButton('Ajouter')->form([
            'user[username]'         => 'testCreation',
            'user[password][first]'  => 'test',
            'user[password][second]' => 'test',
            'user[email]'            => 'testcreation@mail.com'
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'L\'utilisateur a bien été ajouté.');
    }

    /**
     *
     * Test edit user
     *
     */

    public function testEditUser(): void
    {
        $this->client->request('GET', $this->urlGenerator->generate('app_user_edit', ['id' => 1]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects($this->urlGenerator->generate('app_login'));
    }

    public function testEditUserWithUserRole(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'user'])
        );

        $this->client->request('GET', $this->urlGenerator->generate('app_user_edit', ['id' => 1]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testEditUserWithAdminRole(): void
    {
        $this->client->loginUser(
            $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['username' => 'userAdmin'])
        );

        $userId = $this->client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(
            ['username' => 'testCreation']
        )->getId();

        $this->client->request('GET', $this->urlGenerator->generate('app_user_edit', ['id' => $userId]));
        $form = $this->client->getCrawler()->selectButton('Modifier')->form([
            'user[username]'         => 'testModification',
            'user[password][first]'  => 'test',
            'user[password][second]' => 'test',
            'user[email]'            => 'testmodification@mail.com'
        ]);

        $this->client->submit($form);
        $this->client->followRedirect();

        $this->assertSelectorTextContains('div.alert', 'L\'utilisateur a bien été modifié.');
    }

}
