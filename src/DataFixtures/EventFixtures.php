<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $i) {
            $event = (new Event())
                
            ;

            $manager->persist($event);
            $this->addReference(self::class.$i, $event);
        }

        $manager->flush();
    }
}
