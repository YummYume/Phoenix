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
        foreach (range(1, 10) as $i) {
            $event = (new Event())
                ->setDate(new \DateTime())
                ->setName("Fait marquant $i")
                ->setMilestone($this->getReference(MilestoneFixtures::class.$i))
                ->setProject($this->getReference(ProjectFixtures::class.$i))
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
