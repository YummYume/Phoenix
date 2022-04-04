<?php

namespace App\DataFixtures;

use App\Entity\Milestone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MilestoneFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        foreach (range(1, 10) as $i) {
            $milestone = (new Milestone())
                ->setName(ucfirst($faker->words(rand(1, 3), true)))
                ->setRequired($faker->boolean())
                ->setCompleted($faker->boolean())
                ->setProject($this->getReference(ProjectFixtures::class.$i))
            ;

            $manager->persist($milestone);
            $this->addReference(self::class.$i, $milestone);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }
}
