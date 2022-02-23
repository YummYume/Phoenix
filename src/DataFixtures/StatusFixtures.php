<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (range(1, 3) as $i) {
            $status = (new Status())
                ->setSlug("status_$i")
            ;

            $manager->persist($status);
            $this->addReference(self::class.$i, $status);
        }

        $manager->flush();
    }
}
