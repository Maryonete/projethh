<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\President;
use App\Entity\Association;

class EntityCreator
{


    public function createAssociation($code): Association
    {
        $association = new Association();
        $association->setCode($code);
        $association->setLibelle('Asso 1');
        $association->setAdress('adresse');
        $association->setCP('75000');
        $association->setCity('Paris');
        $association->setEmail(uniqid() .  'asso@text.fr');

        $association->setPresident($this->createPresident());
        $reflection = new \ReflectionClass($association);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($association, $code);
        return $association;
    }

    public function createPresident(): President
    {
        $user = new User();
        $user->setFirstname('Test firstname');
        $user->setLastname('Test lastname');
        $user->setEmail(uniqid() .  'test@president.fr');
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $president = new President();
        $president->setUser($user);
        $president->setFonction('président');
        $reflection = new \ReflectionClass($president);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($president, 1);
        return $president;
    }
}
