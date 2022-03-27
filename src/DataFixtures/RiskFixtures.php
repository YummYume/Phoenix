<?php

namespace App\DataFixtures;

use App\Entity\Risk;
use App\Enum\Probability;
use App\Enum\Severity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RiskFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        foreach (range(1, 10) as $i) {
            $risk = (new Risk())
                ->setName(ucfirst($faker->words(rand(1, 3), true)))
                ->setIdentifiedAt($faker->dateTimeBetween('-1 years', 'now'))
                ->setSeverity(Severity::random())
                ->setProbability(Probability::random())
                ->setProject($this->getReference(ProjectFixtures::class.$i))
            ;

            $manager->persist($risk);
            $this->addReference(self::class.$i, $risk);
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
