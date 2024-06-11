<?php

namespace Tests\Unit\Entity;

use App\Entity\Association;
use App\Entity\History;
use App\Entity\User;

use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class HistoryTest.
 *
 * @covers \App\Entity\History
 */
final class HistoryTest extends TestCase
{
    private History $history;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->history = new History();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->history);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(History::class))
            ->getProperty('id');
        $property->setValue($this->history, $expected);
        self::assertSame($expected, $this->history->getId());
    }

    public function testGetStartDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(History::class))
            ->getProperty('startDate');
        $property->setValue($this->history, $expected);
        self::assertSame($expected, $this->history->getStartDate());
    }

    public function testSetStartDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(History::class))
            ->getProperty('startDate');
        $this->history->setStartDate($expected);
        self::assertSame($expected, $property->getValue($this->history));
    }

    public function testGetEndDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(History::class))
            ->getProperty('endDate');
        $property->setValue($this->history, $expected);
        self::assertSame($expected, $this->history->getEndDate());
    }

    public function testSetEndDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(History::class))
            ->getProperty('endDate');
        $this->history->setEndDate($expected);
        self::assertSame($expected, $property->getValue($this->history));
    }

    public function testGetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(History::class))
            ->getProperty('association');
        $property->setValue($this->history, $expected);
        self::assertSame($expected, $this->history->getAssociation());
    }

    public function testSetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(History::class))
            ->getProperty('association');
        $this->history->setAssociation($expected);
        self::assertSame($expected, $property->getValue($this->history));
    }

    public function testGetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(History::class))
            ->getProperty('user');
        $property->setValue($this->history, $expected);
        self::assertSame($expected, $this->history->getUser());
    }

    public function testSetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(History::class))
            ->getProperty('user');
        $this->history->setUser($expected);
        self::assertSame($expected, $property->getValue($this->history));
    }

    public function testRoleGetterAndSetter()
    {
        $expectedRole = 'referent';

        $history = new History();
        $history->setRole($expectedRole);

        $this->assertSame($expectedRole, $history->getRole());
    }
}
