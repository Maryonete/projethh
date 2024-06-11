<?php

namespace Tests\Unit\Controller;

use App\Entity\CampainAssociation;
use App\Controller\VerifyDataController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class VerifyDataControllerTest.
 *
 * @covers \App\Controller\VerifyDataController
 */
final class VerifyDataControllerTest extends WebTestCase
{
    private VerifyDataController $verifyDataController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->verifyDataController = new VerifyDataController();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->verifyDataController);
    }
    public function testVerifyData()
    {
        $client = static::createClient();

        // Replace 'your_token_here' with a valid token for testing
        $token = 'your_token_here';

        // Request the route with the token
        $client->request('GET', '/associationhh/' . $token);

        // Check if the response is successful
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Replace 'your_form_data_here' with valid form data for testing
        $formData = [
            // Your form data here
        ];

        // Submit the form
        $client->submitForm('submit', $formData);

        // Check if the response is successful after submitting the form
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Check if the campAsso object is updated
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $campAssoRepository = $entityManager->getRepository(CampainAssociation::class);
        $campAsso = $campAssoRepository->findOneBy(['token' => $token]);
        $this->assertInstanceOf(CampainAssociation::class, $campAsso);
        $this->assertEquals('updated', $campAsso->getStatut());
    }
}
