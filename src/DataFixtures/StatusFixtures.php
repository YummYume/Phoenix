<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        foreach (range(1, 3) as $i) {
            $status = (new Status())
                ->setTitle(ucfirst($faker->words(rand(1, 3), true)))
                ->setColor($faker->hexColor())
            ;

            $manager->persist($status);
            $this->addReference(self::class.$i, $status);
        }

        $manager->flush();
    }
}
