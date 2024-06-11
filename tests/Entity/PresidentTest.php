<?php

namespace Tests\Unit\Entity;

use App\Entity\Association;
use App\Entity\President;
use App\Entity\User;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class PresidentTest.
 *
 * @covers \App\Entity\President
 */
final class PresidentTest extends TestCase
{
    private President $president;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->president = new President();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->president);
    }

    public function test__toString(): void
    {
        $user = (new User)->setFirstname('John')->setLastname('Do');
        $this->president->setUser($user);
        $expected =  "John Do";
        self::assertSame($expected, $this->president->__toString());
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(President::class))
            ->getProperty('id');
        $property->setValue($this->president, $expected);
        self::assertSame($expected, $this->president->getId());
    }

    public function testGetFonction(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(President::class))
            ->getProperty('fonction');
        $property->setValue($this->president, $expected);
        self::assertSame($expected, $this->president->getFonction());
    }

    public function testSetFonction(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(President::class))
            ->getProperty('fonction');
        $this->president->setFonction($expected);
        self::assertSame($expected, $property->getValue($this->president));
    }

    public function testGetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(President::class))
            ->getProperty('user');
        $property->setValue($this->president, $expected);
        self::assertSame($expected, $this->president->getUser());
    }

    public function testSetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(President::class))
            ->getProperty('user');
        $this->president->setUser($expected);
        self::assertSame($expected, $property->getValue($this->president));
    }

    public function testGetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(President::class))
            ->getProperty('association');
        $property->setValue($this->president, $expected);
        self::assertSame($expected, $this->president->getAssociation());
    }

    public function testSetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(President::class))
            ->getProperty('association');
        $this->president->setAssociation($expected);
        self::assertSame($expected, $property->getValue($this->president));
    }
}
