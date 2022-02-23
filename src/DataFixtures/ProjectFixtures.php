<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 20) as $i) {
            $project = (new Project())
                ->setName("Project$i")
                ->setStatus($this->getReference(StatusFixtures::class.rand(1, 3)))
                ->setStartAt(new \DateTime())
            ;

            $manager->persist($project);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StatusFixtures::class,
        ];
    }
}
