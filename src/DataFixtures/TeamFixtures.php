<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $i) {
            $team = (new Team())
                
            ;

            $manager->persist($team);
            $this->addReference(self::class.$i, $team);
        }

        $manager->flush();
    }
}
