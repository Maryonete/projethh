<?php

namespace Tests\Unit\Controller;

use Mockery;
use App\Entity\User;
use Twig\Environment;
use App\Entity\Campains;
use App\Entity\Association;
use PHPUnit\Framework\TestCase;
use App\Service\StatsCalculator;
use App\Entity\CampainAssociation;
use App\Controller\AdminController;
use App\Repository\CampainsRepository;
use App\Repository\PresidentRepository;
use App\Repository\AssociationRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\CampainAssociationRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AdminControllerTest.
 *
 * @covers \App\Controller\AdminController
 */
final class AdminControllerTest extends WebTestCase
{
    private AdminController $adminController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->adminController = new AdminController();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->adminController);
    }
    //     public function testZeroSentEmails()
    //     {
    //         $campainRepoMock = $this->createMock::mock(CampainsRepository::class);
    // $campainAssoRepoMock = $this->createMock::mock(CampainAssociationRepository::class);
    // $statsCalculatorMock = $this->createMock::mock(StatsCalculator::class);

    // // Mock data with 0 sent emails
    // $campain = new Campains();
    // $campain->setValid(true);
    // $stat = [
    //     'nbAssoCount' => 100,
    //     'nbSentEmailsCount' => 0, // This is the key to test '0' assignment
    //     'nbAssoValidateFormCount' => 10, // Can be any value
    // ];
    //         $campainRepoMock->shouldReceive('findOneByValid')
    //     ->with(true)
    //     ->andReturn($campain);

    // $statsCalculatorMock->shouldReceive('calculateNbAssoCount')
    //     ->andReturn($stat['nbAssoCount']);

    // $statsCalculatorMock->shouldReceive('calculateSentEmailsCount')
    //     ->with($campain->getId())
    //     ->andReturn($stat['nbSentEmailsCount']);

    // $statsCalculatorMock->shouldReceive('calculateNbAssoValidateFormCount')
    //     ->with($campain->getId())
    //     ->andReturn($stat['nbAssoValidateFormCount']);
    //         $controller = new AdminController(
    //             $campainRepoMock,
    //             $campainAssoRepoMock,
    //             $statsCalculatorMock
    //         );

    //         $response = $controller->index();

    //         // Assert that 'percentAssoValidateFormCount' is 0
    //         $this->assertEquals(0, $response->vars()['stat']['percentAssoValidateFormCount']);
    //     }
    public function testIndex()
    {
        $client = static::createClient();
        // Mocking the repositories and services
        $assoRepo = $this->createMock(AssociationRepository::class);
        $campainRepo = $this->createMock(CampainsRepository::class);
        $campainAssoRepo = $this->createMock(CampainAssociationRepository::class);
        $statsCalculator = $this->createMock(StatsCalculator::class);
        $twig = $this->createMock(Environment::class);
        $container = $this->createMock(ContainerInterface::class);

        // Setting up the expected behavior for the mocks
        $campain = $this->createMock(Campains::class);
        $campain->method('getId')->willReturn(1);
        $campain->method('getOldcampain')->willReturn(null);


        $campainAssoRepo->method('findByCampains')->willReturn([]);
        $statsCalculator->method('calculateNbAssoCount')->willReturn(10);
        // Mocking the statsCalculator to return different values for nbSentEmailsCount and nbAssoValidateFormCount
        $statsCalculator->method('calculateSentEmailsCount')->willReturnOnConsecutiveCalls(100, 0);
        $statsCalculator->method('calculateNbAssoValidateFormCount')->willReturnOnConsecutiveCalls(60, 0);

        $assoRepo->method('findAll')->willReturn([]);
        // Admin connecté
        $userRepo = $this->getContainer()->get("doctrine")->getRepository(User::class);
        $user = $userRepo->findOneByEmail('test@test.fr');
        $client->loginUser($user);


        // Calling the method to test
        $crawler = $client->request('GET', '/asso');
        // Get the response
        $response = $client->getResponse();
        // Asserting the response is successful
        $this->assertTrue($response->isSuccessful());

        // Get the content of the response
        $content = $response->getContent();



        // Checking if the content includes expected keys
        $this->assertStringContainsString('AIDE POUR LA COLLECTE DE DON', $content);
    }
}
