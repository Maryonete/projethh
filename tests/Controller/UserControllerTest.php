<?php

namespace Tests\Unit\Controller;

use App\Controller\UserController;
use PHPUnit\Framework\TestCase;

/**
 * Class UserControllerTest.
 *
 * @covers \App\Controller\UserController
 */
final class UserControllerTest extends TestCase
{
    private UserController $userController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->userController = new UserController();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->userController);
    }
}
