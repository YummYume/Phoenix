<?php

namespace App\DataFixtures;

use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TeamFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userCount = 1;

        foreach (range(1, 10) as $i) {
            $team = (new Team())
                ->setName("Team $i")
                ->setResponsible($this->getReference(UserFixtures::class."responsible$i"))
            ;

            foreach (range($userCount, $userCount + 1) as $user) {
                $team->addMember($this->getReference(UserFixtures::class."user$user"));
            }

            $userCount += 2;

            $manager->persist($team);
            $this->addReference(self::class.$i, $team);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
