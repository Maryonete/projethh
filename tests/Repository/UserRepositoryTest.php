<?php

namespace Tests\Unit\Repository;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UserRepositoryTest.
 *
 * @covers \App\Repository\UserRepository
 */
final class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    private ManagerRegistry|MockObject $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->userRepository = new UserRepository($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->userRepository);
        unset($this->registry);
    }
}
