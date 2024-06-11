<?php

namespace Tests\Unit\Repository;

use App\Entity\User;
use App\Entity\President;
use App\Entity\Association;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AssociationRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class AssociationRepositoryTest.
 *
 * @covers \App\Repository\AssociationRepository
 */
final class AssociationRepositoryTest extends TestCase
{
    private AssociationRepository $associationRepository;

    private ManagerRegistry|MockObject $registry;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->associationRepository = new AssociationRepository($this->registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->associationRepository);
        unset($this->registry);
    }



    // public function testFindAllAssociationPresidentEmail()
    // {
    //     $association1 = $this->createMock(Association::class);
    //     $user1 = $this->createMock(User::class);
    //     $user1->setEmail('president1@email.com');
    //     $president1 = $this->createMock(President::class);
    //     $president1->setUser($user1);

    //     $association1->method('getPresident')->willReturn($president1);
    //     $user2 = $this->createMock(User::class);
    //     $user2->setEmail('president2@email.com');
    //     $association2 = $this->createMock(Association::class);
    //     $president2 = $this->createMock(President::class);
    //     $president2->setUser($user2);
    //     $association2->method('getPresident')->willReturn($president2);

    //     $associations = [$association1, $association2];

    //     $repository = $this->createMock(AssociationRepository::class);
    //     // Mock Entity Manager (replace with your actual mocking approach)
    //     $entityManagerMock = $this->createMock(EntityManagerInterface::class);
    //     $entityManagerMock->expects($this->once())
    //         ->method('createQuery')
    //         ->with('SELECT a.id, u.email
    //         FROM App\Entity\Association a
    //         JOIN a.president p
    //         JOIN p.user u
    //         WHERE p.association
    //         IN (:associations)')
    //         ->willReturn($this->createMock(Query::class));

    //     $queryMock = $this->createMock(Query::class);
    //     $queryMock->expects($this->once())
    //         ->method('getResult')
    //         ->willReturn([
    //             ['id' => 1, 'email' => 'president1@email.com'],
    //             ['id' => 2, 'email' => 'president2@email.com'],
    //         ]);



    //     $result = $repository->findAllAssociationPresidentEmail($associations);
    //     dump($result);
    //     die;
    //     $this->assertEquals([
    //         1 => 'president1@email.com',
    //         2 => 'president2@email.com',
    //     ], $result);
    // }
}
