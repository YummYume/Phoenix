<?php

namespace App\DataFixtures;

use App\Entity\Milestone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MilestoneFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $i) {
            $milestone = (new Milestone())
                
            ;

            $manager->persist($milestone);
            $this->addReference(self::class.$i, $milestone);
        }

        $manager->flush();
    }
}
