<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    private UrlGeneratorInterface|null $urlGenerator = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    public function testHomePage(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_home'));

        $homeTitle = "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !";

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_home'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSame(1, $crawler->filter('html:contains("' . $homeTitle . '")')->count());
        $this->assertSame(1, $crawler->filter('h1')->count());
    }
}
