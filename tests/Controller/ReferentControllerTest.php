<?php

namespace Tests\Unit\Controller;

use App\Controller\ReferentController;
use PHPUnit\Framework\TestCase;

/**
 * Class ReferentControllerTest.
 *
 * @covers \App\Controller\ReferentController
 */
final class ReferentControllerTest extends TestCase
{
    private ReferentController $referentController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->referentController = new ReferentController();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->referentController);
    }
}
