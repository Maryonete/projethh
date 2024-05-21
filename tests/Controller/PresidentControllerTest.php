<?php

namespace Tests\Unit\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\PresidentController;
use App\Repository\PresidentRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PresidentControllerTest.
 *
 * @covers \App\Controller\PresidentController
 */
final class PresidentControllerTest extends TestCase
{
    private PresidentController $presidentController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->presidentController = new PresidentController();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->presidentController);
    }

    // public function testIndexReturnsPresidentsList()
    // {
    //     // Create a mock for the PresidentRepository
    //     $presidentRepository = $this->createMock(PresidentRepository::class);

    //     // Mock the findAll method to return an array of presidents
    //     $presidents = [
    //         // ... (sample president data)
    //     ];
    //     $presidentRepository->expects($this->once())
    //         ->method('findAll')
    //         ->willReturn($presidents);

    //     // Create the controller with the mock
    //     $controller = new PresidentController($presidentRepository);

    //     // Create a Request object (optional, depending on your testing framework)
    //     $request = new Request([], [], [], [], [], [], null);

    //     // Call the index method on the controller
    //     $response = $controller->index($presidentRepository);

    //     // Assert that the response is successful
    //     $this->assertEquals(200, $response->getStatusCode());

    //     // Assert that the template is rendered correctly
    //     $this->assertMatchesRegularExpression('/admin\/user\/president\/index\.html\.twig/', $response->getContent());

    //     // Assert that the presidents are passed to the template context
    //     $this->assertStringContainsString('admin/user/presidents', $response->getContent());
    //     // Alternative assertion (depending on Twig template structure):
    //     // $this->assertStringContainsString(json_encode($presidents), $response->getContent());
    // }
}
