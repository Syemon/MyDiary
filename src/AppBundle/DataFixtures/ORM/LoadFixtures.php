<?php

namespace AppBundle\DataFixtures\ORM;

use Nelmio\Alice\Fixtures;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        Fixtures::load(
            __DIR__.'/fixtures.yml',
            $manager,
           [
               'providers' => [$this]
           ]
        );
    }

    public function genus()
    {
        $genera = [
            'Octopus',
            'Balaena',
            'Orcinus',
            'Hippocampus',
            'Asterias',
            'Aurelia',
            'Cucumaria',
            'Chelonia'
        ];

         $key = array_rand($genera);

         return $genera[$key];
    }
}