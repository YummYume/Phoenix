<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        foreach (range(1, 10) as $i) {
            $project = $this->getReference(ProjectFixtures::class.$i);

            $event = (new Event())
                ->setDate($faker->dateTimeBetween($project->getStartAt(), '+3 years'))
                ->setName(ucfirst($faker->words(rand(1, 3), true)))
                ->setDescription($faker->text())
                ->setMilestone($this->getReference(MilestoneFixtures::class.$i))
                ->setProject($project)
            ;

            $manager->persist($event);
            $this->addReference(self::class.$i, $event);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MilestoneFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
