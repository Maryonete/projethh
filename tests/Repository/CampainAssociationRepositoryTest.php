<?php

namespace Tests\Unit\Repository;

use App\Repository\CampainAssociationRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CampainAssociationRepositoryTest.
 *
 * @covers \App\Repository\CampainAssociationRepository
 */
final class CampainAssociationRepositoryTest extends TestCase
{
    private CampainAssociationRepository $campainAssociationRepository;

    private ManagerRegistry|MockObject $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->campainAssociationRepository = new CampainAssociationRepository($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->campainAssociationRepository);
        unset($this->registry);
    }
}
