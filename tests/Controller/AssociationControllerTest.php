<?php

namespace Tests\Unit\Controller;

use App\Entity\User;
use App\Tests\EntityCreator;
use App\Controller\AssociationController;
use App\Repository\AssociationRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AssociationControllerTest.
 *
 * @covers \App\Controller\AssociationController
 */
final class AssociationControllerTest extends WebTestCase
{
    private AssociationController $associationController;
    private KernelBrowser|null $client = null;
    private EntityCreator $entityCreator;
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        // simule le navigateur
        $this->client = static::createClient();

        // EntityCreator
        $this->entityCreator = new EntityCreator();

        $this->associationController = new AssociationController();
        //connexion
        $userRepo = $this->getContainer()->get("doctrine")->getRepository(User::class);
        $user = $userRepo->findOneByEmail('test@test.fr');
        $this->client->loginUser($user);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->associationController);
    }

    public function testListAsso()
    {
        // Create a mock of the AssociationRepository
        $assoRepoMock = $this->createMock(AssociationRepository::class);

        // Define the behavior of the mock: it should return an array of Association objects
        $associations = [
            $this->entityCreator->createAssociation(1),
            $this->entityCreator->createAssociation(2),
            $this->entityCreator->createAssociation(3),
        ];

        $assoRepoMock->method('findBy')->willReturn($associations);

        // Replace the service AssociationRepository with the mock
        $this->client->getContainer()->set('App\Repository\AssociationRepository', $assoRepoMock);

        // Request the '/association' route
        $crawler = $this->client->request('GET', '/association');
        // Assert that the response is successful
        $this->assertResponseIsSuccessful();

        // Assert that the correct template is used
        $this->assertSelectorTextContains('html caption', 'Liste des associations');

        // Assert that the returned associations are in the response
        // $this->assertCount(3, $crawler->filter('tr.association'));

        // Further checks can be added to ensure the order and correctness of the data
    }



    public function testShow()
    {
        // Création de l'entité Association

        $association = $this->entityCreator->createAssociation(1);
        // Utilisation de Reflection pour définir l'ID de l'association
        $reflection = new \ReflectionClass($association);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($association, 1);

        // Mock du repository AssociationRepository
        $assoRepoMock = $this->createMock(AssociationRepository::class);
        $assoRepoMock->method('find')->willReturn($association);
        $assoRepoMock->method('findOneBy')->willReturn($association);

        // Configuration du container de services pour utiliser notre mock
        $this->getContainer()->set('App\Repository\AssociationRepository', $assoRepoMock);

        // Requête sur la route /association/1
        $this->client->request('GET', '/association/1');

        // Vérifie que la réponse est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie que le template correct est utilisé et que le titre H1 contient "Association Asso 1"
        $this->assertSelectorTextContains('html h1', 'Association Asso 1');
    }
}
