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
        $portfolioCount = 1;
        $faker = \Faker\Factory::create('fr_FR');

        foreach (range(1, 10) as $i) {
            $project = (new Project())
                ->setName(ucfirst($faker->words(rand(1, 3), true)))
                ->setDescription($faker->text())
                ->setStatus($this->getReference(StatusFixtures::class.rand(1, 3)))
                ->setStartAt($faker->dateTimeBetween('-1 years', '+1 years'))
                ->addPortfolio($this->getReference(PortfolioFixtures::class.$portfolioCount))
                ->setTeam($this->getReference(TeamFixtures::class.$i))
                ->setBudget($this->getReference(BudgetFixtures::class.$i))
                ->setArchived($faker->boolean(10))
            ;

            if (0 !== $portfolioCount % 2) {
                ++$portfolioCount;
            }

            $this->addReference(self::class.$i, $project);
            $manager->persist($project);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            StatusFixtures::class,
            PortfolioFixtures::class,
            TeamFixtures::class,
            BudgetFixtures::class,
        ];
    }
}
