<?php

namespace Tests\Unit\Entity;

use App\Entity\Association;
use App\Entity\Referent;
use App\Entity\User;
use ReflectionClass;
use PHPUnit\Framework\TestCase;

/**
 * Class ReferentTest.
 *
 * @covers \App\Entity\Referent
 */
final class ReferentTest extends TestCase
{
    private Referent $referent;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->referent = new Referent();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->referent);
    }

    public function test__toString(): void
    {
        $user = (new User)->setFirstname('John')->setLastname('Do');
        $this->referent->setUser($user);
        $expected =  "John Do";
        self::assertSame($expected, $this->referent->__toString());
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('id');
        $property->setValue($this->referent, $expected);
        self::assertSame($expected, $this->referent->getId());
    }

    public function testGetTel(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('tel');
        $property->setValue($this->referent, $expected);
        self::assertSame($expected, $this->referent->getTel());
    }

    public function testSetTel(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('tel');
        $this->referent->setTel($expected);
        self::assertSame($expected, $property->getValue($this->referent));
    }

    public function testGetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('user');
        $property->setValue($this->referent, $expected);
        self::assertSame($expected, $this->referent->getUser());
    }

    public function testSetUser(): void
    {
        $expected = $this->createMock(User::class);
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('user');
        $this->referent->setUser($expected);
        self::assertSame($expected, $property->getValue($this->referent));
    }

    public function testGetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('association');
        $property->setValue($this->referent, $expected);
        self::assertSame($expected, $this->referent->getAssociation());
    }

    public function testSetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(Referent::class))
            ->getProperty('association');
        $this->referent->setAssociation($expected);
        self::assertSame($expected, $property->getValue($this->referent));
    }
}
