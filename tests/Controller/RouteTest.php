<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RouteTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        // simule le navigateur
        $this->client = static::createClient();

        $userRepo = $this->getContainer()->get("doctrine")->getRepository(User::class);
        $user = $userRepo->findOneByEmail('test@test.fr');
        $this->client->loginUser($user);
    }
    /**
     * PHPUnit's data providers allow to execute the same tests repeated times
     * using a different set of data each time.
     *
     * @dataProvider getPublicUrls
     */
    public function testPublicUrls(string $url): void
    {
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful(sprintf('The %s URL loads correctly.', $url));
    }
    public function getPublicUrls(): \Generator
    {
        // yield ['/asso'];
        yield ['/association'];
        yield ['/association/new'];
        yield ['/campains/'];
        yield ['/campains/new'];
        yield ['/file_new'];
        // yield ['/president/'];
        yield ['/president/new'];
        yield ['/referent/new'];
        // yield ['/'];
        yield ['/user/'];
    }
}
