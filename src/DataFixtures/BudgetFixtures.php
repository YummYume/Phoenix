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
            $initialAmount = rand(1, 10000);
            $spentAmount = rand(1, $initialAmount);

            $budget = (new Budget())
                ->setInitialAmount($initialAmount)
                ->setSpentAmount($spentAmount)
            ;

            $manager->persist($budget);
            $this->addReference(self::class.$i, $budget);
        }

        $manager->flush();
    }
}
