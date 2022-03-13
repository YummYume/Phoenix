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

        foreach (range(1, 10) as $i) {
            $project = (new Project())
                ->setName("Projet $i")
                ->setStatus($this->getReference(StatusFixtures::class.rand(1, 3)))
                ->setStartAt(new \DateTime())
                ->setPortfolio($this->getReference(PortfolioFixtures::class.$portfolioCount))
                ->setTeam($this->getReference(TeamFixtures::class.$i))
                ->setBudget($this->getReference(BudgetFixtures::class.$i))
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
