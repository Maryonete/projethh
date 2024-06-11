<?php

namespace Tests\Unit\Entity;

use ReflectionClass;
use App\Entity\History;
use App\Entity\Referent;
use App\Entity\President;
use App\Entity\Association;
use PHPUnit\Framework\TestCase;
use App\Entity\CampainAssociation;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AssociationTest.
 *
 * @covers \App\Entity\Association
 */
final class AssociationTest extends TestCase
{
    private Association $association;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->association = new Association();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->association);
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Association::class))
            ->getProperty('id');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getId());
    }

    public function testGetCode(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Association::class))
            ->getProperty('code');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getCode());
    }

    public function testSetCode(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Association::class))
            ->getProperty('code');
        $this->association->setCode($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetLibelle(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('libelle');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getLibelle());
    }

    public function testSetLibelle(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('libelle');
        $this->association->setLibelle($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetAdress(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('adress');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getAdress());
    }

    public function testSetAdress(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('adress');
        $this->association->setAdress($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetCp(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('cp');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getCp());
    }

    public function testSetCp(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('cp');
        $this->association->setCp($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetCity(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('city');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getCity());
    }

    public function testSetCity(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('city');
        $this->association->setCity($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetTel(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('tel');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getTel());
    }

    public function testSetTel(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('tel');
        $this->association->setTel($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetEmail(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('email');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getEmail());
    }

    public function testSetEmail(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Association::class))
            ->getProperty('email');
        $this->association->setEmail($expected);
        self::assertSame($expected, $property->getValue($this->association));
    }

    public function testGetCampainAssociations()
    {
        $association = new Association();
        $campainAssociation1 = new CampainAssociation();
        $campainAssociation2 = new CampainAssociation();

        $association->addCampainAssociation($campainAssociation1);
        $association->addCampainAssociation($campainAssociation2);

        $this->assertCount(2, $association->getCampainAssociations());
        $this->assertTrue($association->getCampainAssociations()->contains($campainAssociation1));
        $this->assertTrue($association->getCampainAssociations()->contains($campainAssociation2));
    }
    public function testAddCampainAssociation()
    {
        $association = new Association();
        $campainAssociation = new CampainAssociation();

        $this->assertCount(0, $association->getCampainAssociations());

        $association->addCampainAssociation($campainAssociation);

        $this->assertCount(1, $association->getCampainAssociations());
        $this->assertTrue($association->getCampainAssociations()->contains($campainAssociation));
        $this->assertSame($association, $campainAssociation->getAssociation());
    }

    public function testRemoveCampainAssociation()
    {
        $association = new Association();
        $campainAssociation = new CampainAssociation();

        $association->addCampainAssociation($campainAssociation);
        $this->assertCount(1, $association->getCampainAssociations());

        $association->removeCampainAssociation($campainAssociation);

        $this->assertCount(0, $association->getCampainAssociations());
        $this->assertFalse($association->getCampainAssociations()->contains($campainAssociation));
        $this->assertNull($campainAssociation->getAssociation());
    }


    public function testGetPresident()
    {
        $association = new Association();
        $this->assertNull($association->getPresident());

        $president = new President();
        $association->setPresident($president);
        $this->assertSame($president, $association->getPresident());
    }

    public function testSetPresident()
    {
        $association = new Association();
        $president1 = new President();
        $president2 = new President();

        // Vérifie que le président est correctement défini
        $association->setPresident($president1);
        $this->assertSame($president1, $association->getPresident());

        // Vérifie que l'association est correctement définie sur le président
        $this->assertSame($association, $president1->getAssociation());

        // Vérifie que l'association précédente du président est correctement supprimée
        $association->setPresident($president2);
        $this->assertNull($president1->getAssociation());

        // Vérifie que le nouveau président est correctement défini
        $this->assertSame($president2, $association->getPresident());

        // Vérifie que l'association est correctement définie sur le nouveau président
        $this->assertSame($association, $president2->getAssociation());
    }

    // public function testSetPresidentToNull()
    // {
    //     $association = new Association();
    //     $oldPresident = $this->createMock(President::class);

    //     $oldPresident->expects($this->once())
    //         ->method('setAssociation')
    //         ->with(null);

    //     $association->setPresident($oldPresident);
    //     $association->setPresident(null);

    //     $this->assertNull($association->getPresident());
    // }
    public function testGetHistories()
    {
        $association = new Association();
        $history1 = $this->createMock(History::class);
        $history2 = $this->createMock(History::class);

        $association->addHistory($history1);
        $association->addHistory($history2);

        $histories = $association->getHistories();

        $this->assertInstanceOf(ArrayCollection::class, $histories);
        $this->assertCount(2, $histories);
        $this->assertTrue($histories->contains($history1));
        $this->assertTrue($histories->contains($history2));
    }

    public function testAddHistory()
    {
        $association = new Association();
        $history = $this->createMock(History::class);

        $this->assertCount(0, $association->getHistories());

        $history->expects($this->once())
            ->method('setAssociation')
            ->with($association);

        $association->addHistory($history);

        $this->assertCount(1, $association->getHistories());
        $this->assertTrue($association->getHistories()->contains($history));
    }
    public function testAddHistoryDoesntAddDuplicate()
    {
        $association = new Association();
        $historyMock = $this->createMock(History::class);

        $association->addHistory($historyMock);
        $association->addHistory($historyMock); // This shouldn't be added again

        $this->assertTrue($association->getHistories()->count() === 1);
    }
    public function testRemoveHistory()
    {
        $association = new Association();
        $history = new History();
        $association->addHistory($history);

        // Vérifie que l'histoire est initialement associée à l'association
        $this->assertSame($association, $history->getAssociation());

        // Supprime l'histoire de l'association
        $association->removeHistory($history);

        // Vérifie que l'histoire a été correctement supprimée de l'association
        $this->assertCount(0, $association->getHistories());
        $this->assertNull($history->getAssociation());
    }
    public function testGetReferent(): void
    {
        $expected = $this->createMock(Referent::class);
        $property = (new ReflectionClass(Association::class))
            ->getProperty('referent');
        $property->setValue($this->association, $expected);
        self::assertSame($expected, $this->association->getReferent());
    }
    public function testSetReferent()
    {
        $association = new Association();
        $referent1 = new Referent();
        $referent2 = new Referent();

        // Associe le premier référent à l'association
        $association->setReferent($referent1);

        // Vérifie que le premier référent est correctement associé à l'association
        $this->assertSame($association, $referent1->getAssociation());

        // Associe le deuxième référent à l'association, remplaçant ainsi le premier
        $association->setReferent($referent2);

        // Vérifie que le premier référent n'est plus associé à l'association
        $this->assertNull($referent1->getAssociation());

        // Vérifie que le deuxième référent est correctement associé à l'association
        $this->assertSame($association, $referent2->getAssociation());
    }
}
