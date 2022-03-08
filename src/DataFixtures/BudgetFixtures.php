<?php

namespace App\DataFixtures;

use App\Entity\Budget;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BudgetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 10) as $i) {
            $budget = (new Budget())
                
            ;

            $manager->persist($budget);
            $this->addReference(self::class.$i, $budget);
        }

        $manager->flush();
    }
}
