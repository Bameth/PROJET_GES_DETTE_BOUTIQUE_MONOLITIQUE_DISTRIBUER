<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DetteFixtures extends Fixture
{
    
    public function load(ObjectManager $manager): void
    {
       
        // Remplir la BD avec les dettes
        $manager->flush();
    }
}