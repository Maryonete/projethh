<?php

use PHPUnit\Framework\TestCase;
use App\Enum\Role;

class RoleTest extends TestCase
{
    public function testIsSupported()
    {
        // Test avec une valeur supportée
        $this->assertTrue(Role::isSupported(Role::PRESIDENT));

        // Test avec une valeur non supportée
        $this->assertFalse(Role::isSupported('invalid'));
    }

    public function testGetConstants()
    {
        // Récupérer les constantes de Role
        $constants = Role::getConstants();

        // Vérifier que les constantes sont correctes
        $this->assertContains(Role::PRESIDENT, $constants);
        $this->assertContains(Role::REFERENT, $constants);
        $this->assertCount(2, $constants); // Vérifier qu'il y a exactement deux constantes
    }
}
