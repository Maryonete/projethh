<?php

namespace Tests\Unit\Repository;

use App\Repository\CampainsRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class CampainsRepositoryTest.
 *
 * @covers \App\Repository\CampainsRepository
 */
final class CampainsRepositoryTest extends TestCase
{
    private CampainsRepository $campainsRepository;

    private ManagerRegistry|MockObject $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->campainsRepository = new CampainsRepository($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->campainsRepository);
        unset($this->registry);
    }
}
