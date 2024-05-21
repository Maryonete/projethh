<?php

namespace Tests\Unit\Repository;

use App\Repository\PresidentRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class PresidentRepositoryTest.
 *
 * @covers \App\Repository\PresidentRepository
 */
final class PresidentRepositoryTest extends TestCase
{
    private PresidentRepository $presidentRepository;

    private ManagerRegistry|MockObject $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->presidentRepository = new PresidentRepository($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->presidentRepository);
        unset($this->registry);
    }
}
