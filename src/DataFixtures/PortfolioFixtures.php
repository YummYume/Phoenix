<?php

namespace App\DataFixtures;

use App\Entity\Portfolio;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PortfolioFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $i) {
            $portfolio = (new Portfolio())
                
            ;

            $manager->persist($portfolio);
            $this->addReference(self::class.$i, $portfolio);
        }

        $manager->flush();
    }
}
