<?php

namespace Tests\Unit\Service;

use App\Repository\AssociationRepository;
use App\Repository\CampainAssociationRepository;
use App\Service\StatsCalculator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class StatsCalculatorTest.
 *
 * @covers \App\Service\StatsCalculator
 */
final class StatsCalculatorTest extends TestCase
{
    private StatsCalculator $statsCalculator;

    private EntityManagerInterface|MockObject $entityManager;

    private AssociationRepository|MockObject $associationRepository;

    private CampainAssociationRepository|MockObject $campainAssociationRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->associationRepository = $this->createMock(AssociationRepository::class);
        $this->campainAssociationRepository = $this->createMock(CampainAssociationRepository::class);
        $this->statsCalculator = new StatsCalculator($this->entityManager, $this->associationRepository, $this->campainAssociationRepository);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->statsCalculator);
        unset($this->entityManager);
        unset($this->associationRepository);
        unset($this->campainAssociationRepository);
    }
}
