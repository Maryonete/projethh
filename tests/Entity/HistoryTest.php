<?php

namespace Tests\Unit\Entity;

use App\Entity\Association;
use App\Entity\Traces;
use App\Entity\User;

use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class TracesTest.
 *
 * @covers \App\Entity\Traces
 */
final class TracesTest extends TestCase
{
    private Traces $traces;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->traces = new Traces();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->traces);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('id');
        $property->setValue($this->traces, $expected);
        self::assertSame($expected, $this->traces->getId());
    }

    public function testGetStartDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('startDate');
        $property->setValue($this->traces, $expected);
        self::assertSame($expected, $this->traces->getStartDate());
    }

    public function testSetStartDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('startDate');
        $this->traces->setStartDate($expected);
        self::assertSame($expected, $property->getValue($this->traces));
    }

    public function testGetEndDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('endDate');
        $property->setValue($this->traces, $expected);
        self::assertSame($expected, $this->traces->getEndDate());
    }

    public function testSetEndDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('endDate');
        $this->traces->setEndDate($expected);
        self::assertSame($expected, $property->getValue($this->traces));
    }

    public function testGetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('association');
        $property->setValue($this->traces, $expected);
        self::assertSame($expected, $this->traces->getAssociation());
    }

    public function testSetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('association');
        $this->traces->setAssociation($expected);
        self::assertSame($expected, $property->getValue($this->traces));
    }

    public function testGetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('user');
        $property->setValue($this->traces, $expected);
        self::assertSame($expected, $this->traces->getUser());
    }

    public function testSetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(Traces::class))
            ->getProperty('user');
        $this->traces->setUser($expected);
        self::assertSame($expected, $property->getValue($this->traces));
    }

    public function testRoleGetterAndSetter()
    {
        $expectedRole = 'referent';

        $traces = new Traces();
        $traces->setRole($expectedRole);

        $this->assertSame($expectedRole, $traces->getRole());
    }
}
