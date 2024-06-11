<?php

use PHPUnit\Framework\TestCase;
use App\Enum\AbstractEnum;

// Créer une classe concrète de test étendant AbstractEnum
class ConcreteEnum extends AbstractEnum
{
    // Implémenter la méthode abstraite getConstants avec les constantes de test
    public static function getConstants(): array
    {
        return ['VALUE_1' => 'value1', 'VALUE_2' => 'value2'];
    }
}

class AbstractEnumTest extends TestCase
{
    public function testIsSupported()
    {
        // Créer une instance de la classe de test concrète
        $enum = new ConcreteEnum();

        // Tester une valeur supportée
        $this->assertTrue($enum::isSupported('value1'));

        // Tester une valeur non supportée
        $this->assertFalse($enum::isSupported('invalid'));
    }


    public function testGetConstants()
    {
        // Créer une instance de la classe de test concrète
        $enum = new ConcreteEnum();

        // Obtenir les constantes de l'énumération
        $constants = $enum::getConstants();

        // Vérifier que les constantes sont correctement récupérées
        $this->assertIsArray($constants);
        $this->assertArrayHasKey('VALUE_1', $constants);
        $this->assertEquals('value1', $constants['VALUE_1']);
        $this->assertArrayHasKey('VALUE_2', $constants);
        $this->assertEquals('value2', $constants['VALUE_2']);
    }
}
