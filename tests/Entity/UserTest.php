<?php

namespace Tests\Unit\Entity;

use App\Entity\User;
use ReflectionClass;
use App\Entity\Traces;
use App\Entity\President;
use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\Collection;

/**
 * Class UserTest.
 *
 * @covers \App\Entity\User
 */
final class UserTest extends TestCase
{
    private User $user;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->user);
    }


    public function test__toString(): void
    {
        $this->user->setFirstname('John')->setLastname('Do');
        $expected =  "John Do";
        self::assertSame($expected, $this->user->__toString());
    }

    public function testGetId(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(User::class))
            ->getProperty('id');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getId());
    }

    public function testGetEmail(): void
    {
        $expected = 'test@test.fr';
        $property = (new ReflectionClass(User::class))
            ->getProperty('email');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getEmail());
    }

    public function testSetEmail(): void
    {
        $expected = 'test@test.fr';
        $property = (new ReflectionClass(User::class))
            ->getProperty('email');
        $this->user->setEmail($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }

    public function testGetUserIdentifier(): void
    {
        $this->user->setEmail('mail@test.fr');
        $expected =  "mail@test.fr";
        self::assertSame($expected, $this->user->getUserIdentifier());
    }

    public function testRoles(): void
    {
        // Test setRoles and getRoles
        $roles = ['ROLE_ADMIN'];
        $this->user->setRoles($roles);
        self::assertSame($roles, $this->user->getRoles());

        // Test adding roles
        $additionalRoles = ['ROLE_MANAGER', 'ROLE_EDITOR'];
        $expectedRoles = array_merge($roles, $additionalRoles, ['ROLE_USER']);
        $this->user->setRoles($expectedRoles);
        self::assertSame($expectedRoles, $this->user->getRoles());
    }
    public function testGetPassword(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('password');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getPassword());
    }

    public function testSetPassword(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('password');
        $this->user->setPassword($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }
    public function testGetAndSetEmail(): void
    {

        self::assertNull($this->user->getEmail());
        $email = 'test@studi.fr';
        $this->user->setEmail($email);
        self::assertSame($email, $this->user->getEmail());
    }


    public function testSetBadEmail()
    {
        $this->expectException(\TypeError::class);
        $expected = 'teststudi.fr';
        $this->user->setEmail($expected);
    }

    public function testGetFirstname(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('firstname');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getFirstname());
    }

    public function testSetFirstname(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('firstname');
        $this->user->setFirstname($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }

    public function testGetLastname(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('lastname');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getLastname());
    }

    public function testSetLastname(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(User::class))
            ->getProperty('lastname');
        $this->user->setLastname($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }

    public function testGetPresident(): void
    {
        $expected = $this->createMock(President::class);
        $property = (new ReflectionClass(User::class))
            ->getProperty('president');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getPresident());
    }

    public function testSetPresident(): void
    {
        $expected = $this->createMock(President::class);
        $property = (new ReflectionClass(User::class))
            ->getProperty('president');
        $this->user->setPresident($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }

    public function testGetReferent(): void
    {
        $expected = null;
        $property = (new ReflectionClass(User::class))
            ->getProperty('referent');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getReferent());
    }

    public function testSetReferent(): void
    {
        $expected = null;
        $property = (new ReflectionClass(User::class))
            ->getProperty('referent');
        $this->user->setReferent($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }

    public function testGetPlainPassword(): void
    {
        $expected = null;
        $property = (new ReflectionClass(User::class))
            ->getProperty('plainPassword');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getPlainPassword());
    }

    public function testSetPlainPassword(): void
    {
        $expected = null;
        $property = (new ReflectionClass(User::class))
            ->getProperty('plainPassword');
        $this->user->setPlainPassword($expected);
        self::assertSame($expected, $property->getValue($this->user));
    }

    public function testGetHistories(): void
    {
        $expected = $this->createMock(Collection::class);
        $property = (new ReflectionClass(User::class))
            ->getProperty('histories');
        $property->setValue($this->user, $expected);
        self::assertSame($expected, $this->user->getHistories());
    }
    public function testAddAndRemoveTraces()
    {
        // Crée une instance de la classe User
        $user = new User();

        // Crée une instance de la classe Traces
        $traces = new Traces();

        // Ajoute l'historique à l'utilisateur
        $user->addTraces($traces);

        // Vérifie que l'historique a été ajouté avec succès à l'utilisateur
        $this->assertCount(1, $user->getHistories());
        $this->assertSame($user, $traces->getUser());

        // Supprime l'historique de l'utilisateur
        $user->removeTraces($traces);

        // Vérifie que l'historique a été supprimé avec succès de l'utilisateur
        $this->assertCount(0, $user->getHistories());
        $this->assertNull($traces->getUser());
    }
}
