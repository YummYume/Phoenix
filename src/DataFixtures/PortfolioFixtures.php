<?php

namespace App\DataFixtures;

use App\Entity\Portfolio;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PortfolioFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        foreach (range(1, 10) as $i) {
            $portfolio = (new Portfolio())
                ->setName(ucfirst($faker->words(rand(1, 3), true)))
                ->setResponsible($this->getReference(UserFixtures::class."responsible$i"))
                ->addProject($this->getReference(ProjectFixtures::class.$i))
            ;

            $manager->persist($portfolio);
            $this->addReference(self::class.$i, $portfolio);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ProjectFixtures::class,
        ];
    }
}
