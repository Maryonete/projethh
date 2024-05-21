<?php

namespace Tests\Unit\Entity;

use DateTime;
use ReflectionClass;

use App\Entity\Campains;
use Campains as CampainsAlias;
use PHPUnit\Framework\TestCase;
use App\Entity\CampainAssociation;
use Doctrine\Common\Collections\Collection;

/**
 * Class CampainsTest.
 *
 * @covers \App\Entity\Campains
 */
final class CampainsTest extends TestCase
{
    private Campains $campains;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->campains = new Campains();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->campains);
    }

    public function test__toString(): void
    {
        $expected = "Libelle association";
        $this->campains->setLibelle($expected);
        self::assertSame($expected, $this->campains->__toString());
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('id');
        $property->setValue($this->campains, $expected);
        self::assertSame($expected, $this->campains->getId());
    }

    public function testGetLibelle(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('libelle');
        $property->setValue($this->campains, $expected);
        self::assertSame($expected, $this->campains->getLibelle());
    }

    public function testSetLibelle(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('libelle');
        $this->campains->setLibelle($expected);
        self::assertSame($expected, $property->getValue($this->campains));
    }

    public function testGetDate(): void
    {
        $expected = new \DateTime(); // Utilisation de la classe DateTime réelle
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('date');
        $property->setValue($this->campains, $expected);
        self::assertSame($expected, $this->campains->getDate());
    }


    public function testSetDate(): void
    {
        $expected = new \DateTime();
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('date');
        $this->campains->setDate($expected);
        self::assertSame($expected, $property->getValue($this->campains));
    }

    public function testGetCampainAssociations(): void
    {
        $expected = $this->createMock(Collection::class);
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('campainAssociations');
        $property->setValue($this->campains, $expected);
        self::assertSame($expected, $this->campains->getCampainAssociations());
    }

    public function testAddCampainAssociation()
    {
        $campainAssociation = new CampainAssociation();

        $this->assertFalse($this->campains->getCampainAssociations()->contains($campainAssociation));

        $this->campains->addCampainAssociation($campainAssociation);

        $this->assertTrue($this->campains->getCampainAssociations()->contains($campainAssociation));
        // $campainAssociation->expects($this->once())->method('setCampains')->with($this->campains);

        $this->assertSame($this->campains, $campainAssociation->getCampains());

        $returnedInstance = $this->campains->addCampainAssociation($campainAssociation);

        $this->assertSame($this->campains, $returnedInstance); // Verify method chaining

    }

    public function testRemoveCampainAssociation(): void
    {
        $campainAssociation = $this->createMock(CampainAssociation::class);

        $this->assertFalse($this->campains->getCampainAssociations()->contains($campainAssociation)); // Assert initial state

        $this->campains->addCampainAssociation($campainAssociation);
        $this->campains->removeCampainAssociation($campainAssociation);
        $this->assertFalse($this->campains->getCampainAssociations()->contains($campainAssociation)); // Assert initial state
    }

    public function testGetDateSendShouldReturnDateTimeOrNull()
    {


        // Test lorsque la date n'est pas définie
        $this->assertNull($this->campains->getDateSend());

        // Création d'un objet DateTime pour simuler une date
        $date = new DateTime('2022-05-01');

        // Test après avoir défini une date
        $this->campains->setDateSend($date);
        $this->assertEquals($date, $this->campains->getDateSend());
    }

    public function testSetDateSendShouldSetDateTime()
    {


        // Création d'un objet DateTime pour simuler une date
        $date = new DateTime('2022-05-01');

        // Appel de la méthode setDateSend pour définir une date
        $this->campains->setDateSend($date);

        // Vérification que la date a été correctement définie
        $this->assertEquals($date, $this->campains->getDateSend());
    }


    public function testGetAndSetObjetEmail()
    {
        // Test lorsque l'objet email n'est pas défini
        $this->assertNull($this->campains->getObjetEmail());

        // Définir un objet email
        $objetEmail = "Objet de l'email";
        $this->campains->setObjetEmail($objetEmail);

        // Vérifier si l'objet email a été correctement défini
        $this->assertEquals($objetEmail, $this->campains->getObjetEmail());

        // Test avec une nouvelle valeur
        $nouvelObjetEmail = "Nouvel objet de l'email";
        $this->campains->setObjetEmail($nouvelObjetEmail);

        // Vérifier si la nouvelle valeur a été correctement définie
        $this->assertEquals($nouvelObjetEmail, $this->campains->getObjetEmail());
    }

    public function testGetAndSetTexteEmail()
    {
        // Test lorsque le texte de l'email n'est pas défini
        $this->assertNull($this->campains->getTexteEmail());

        // Définir un texte de l'email
        $texteEmail = "Texte de l'email";
        $this->campains->setTexteEmail($texteEmail);

        // Vérifier si le texte de l'email a été correctement défini
        $this->assertEquals($texteEmail, $this->campains->getTexteEmail());

        // Test avec une nouvelle valeur
        $nouveauTexteEmail = "Nouveau texte de l'email";
        $this->campains->setTexteEmail($nouveauTexteEmail);

        // Vérifier si la nouvelle valeur a été correctement définie
        $this->assertEquals($nouveauTexteEmail, $this->campains->getTexteEmail());
    }

    public function testGetDestinataire(): void
    {
        $expected = [];
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('destinataire');
        $property->setValue($this->campains, $expected);
        self::assertSame($expected, $this->campains->getDestinataire());
    }

    public function testSetDestinataire(): void
    {
        $expected = [];
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('destinataire');
        $this->campains->setDestinataire($expected);
        self::assertSame($expected, $property->getValue($this->campains));
    }

    public function testGetAndSetEmailFrom(): void
    {

        // Définir un texte de l'email
        $emailFrom = "test@free.fr";
        $this->campains->setEmailFrom($emailFrom);

        // Vérifier si le texte de l'email a été correctement défini
        $this->assertEquals($emailFrom, $this->campains->getEmailFrom());

        // Test avec une nouvelle valeur
        $nouveauEmailFrom = "test2@free.fr";
        $this->campains->setEmailFrom($nouveauEmailFrom);

        // Vérifier si la nouvelle valeur a été correctement définie
        $this->assertEquals($nouveauEmailFrom, $this->campains->getEmailFrom());
    }

    public function testGetAndSetEmailCc(): void
    {
        // Test lorsque le texte de l'email n'est pas défini
        $this->assertNull($this->campains->getEmailCC());

        // Définir un texte de l'email
        $emailCC = "test@free.fr";
        $this->campains->setEmailCc($emailCC);

        // Vérifier si le texte de l'email a été correctement défini
        $this->assertEquals($emailCC, $this->campains->getEmailCC());

        // Test avec une nouvelle valeur
        $nouveauEmailCC = "test2@free.fr";
        $this->campains->setEmailCc($nouveauEmailCC);

        // Vérifier si la nouvelle valeur a été correctement définie
        $this->assertEquals($nouveauEmailCC, $this->campains->getEmailCC());
    }

    public function testGetOldcampain(): void
    {
        $expected = $this->createMock(Campains::class);
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('oldcampain');
        $property->setValue($this->campains, $expected);
        self::assertSame($expected, $this->campains->getOldcampain());
    }

    public function testSetOldcampain(): void
    {
        $expected = $this->createMock(Campains::class);
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('oldcampain');
        $this->campains->setOldcampain($expected);
        self::assertSame($expected, $property->getValue($this->campains));
    }



    public function testSetValid(): void
    {
        $expected = true;
        $property = (new ReflectionClass(Campains::class))
            ->getProperty('valid');
        $this->campains->setValid($expected);
        $this->assertTrue($this->campains->isValid());
        self::assertSame($expected, $property->getValue($this->campains));
    }
}
