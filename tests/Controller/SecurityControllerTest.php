<?php

namespace Tests\Unit\Controller;

use App\Entity\User;
use App\Controller\SecurityController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest.
 *
 * @covers \App\Controller\SecurityController
 */
final class SecurityControllerTest extends WebTestCase
{
    private SecurityController $securityController;
    private KernelBrowser|null $client = null;
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->securityController = new SecurityController();
        // simule le navigateur
        $this->client = static::createClient();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->securityController);
    }
    public function testDisplayLogin(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }
    public function testLoginWithBadCredential(): void
    {
        $crawler = $this->client->request('GET', '/');
        $form = $crawler->selectButton('Connexion')->form([
            'email'     =>  'jo@staudi.fr',
            'password'  =>  'fakepassword',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
    public function testSuccessfullLoginAsAdmin(): void
    {
        $userRepo = $this->getContainer()->get("doctrine")->getRepository(User::class);
        $user = $userRepo->findOneByEmail('test@test.fr');
        $this->client->loginUser($user);

        $this->client->request('GET', '/');

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'AIDE POUR LA COLLECTE DE DON');
    }
    public function testLogoutThrowsException()
    {
        $controller = new SecurityController();

        // Assert that the function throws a LogicException
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('This method can be blank - it will be intercepted by the logout key on your firewall.');

        $controller->logout();
    }
}
