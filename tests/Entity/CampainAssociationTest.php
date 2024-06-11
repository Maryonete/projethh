<?php

namespace Tests\Unit\Entity;

use ReflectionClass;
use DateTimeImmutable;
use App\Entity\Campains;
use App\Entity\Association;
use PHPUnit\Framework\TestCase;
use App\Entity\CampainAssociation;


/**
 * Class CampainAssociationTest.
 *
 * @covers \App\Entity\CampainAssociation
 */
final class CampainAssociationTest extends TestCase
{
    private CampainAssociation $campainAssociation;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->campainAssociation = new CampainAssociation();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->campainAssociation);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('id');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getId());
    }

    public function testGetStatut(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('statut');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getStatut());
    }

    public function testSetStatut(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('statut');
        $this->campainAssociation->setStatut($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }
    public function testGetAndSetTextePersonnalise(): void
    {
        $expected = '42';
        $this->campainAssociation->setTextePersonnalise($expected);
        self::assertSame($expected, $this->campainAssociation->getTextePersonnalise());
    }
    public function testGetAndSetUpdatedBy(): void
    {
        $expected = '42';
        $this->campainAssociation->setUpdatedBy($expected);
        self::assertSame($expected, $this->campainAssociation->getUpdatedBy());
    }



    public function testGetUpdatedTextAt(): void
    {
        $expected = $this->createMock(DateTimeImmutable::class);
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('updatedTextAt');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getUpdatedTextAt());
    }

    public function testSetUpdatedTextAt(): void
    {
        $expected = $this->createMock(DateTimeImmutable::class);
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('updatedTextAt');
        $this->campainAssociation->setUpdatedTextAt($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }

    public function testGetCampains(): void
    {
        $expected = $this->createMock(Campains::class);
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('campains');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getCampains());
    }

    public function testSetCampains(): void
    {
        $expected = $this->createMock(Campains::class);
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('campains');
        $this->campainAssociation->setCampains($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }

    public function testGetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('association');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getAssociation());
    }

    public function testSetAssociation(): void
    {
        $expected = $this->createMock(Association::class);
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('association');
        $this->campainAssociation->setAssociation($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }

    public function testGetEmails(): void
    {
        $expected = null;
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('emails');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getEmails());
    }

    public function testSetEmails(): void
    {
        $expected = null;
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('emails');
        $this->campainAssociation->setEmails($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }

    public function testGetSendAt(): void
    {
        $expected = null;
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('sendAt');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getSendAt());
    }

    public function testSetSendAt(): void
    {
        $expected = null;
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('sendAt');
        $this->campainAssociation->setSendAt($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }

    public function testGetToken(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('token');
        $property->setValue($this->campainAssociation, $expected);
        self::assertSame($expected, $this->campainAssociation->getToken());
    }

    public function testSetToken(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(CampainAssociation::class))
            ->getProperty('token');
        $this->campainAssociation->setToken($expected);
        self::assertSame($expected, $property->getValue($this->campainAssociation));
    }
}
