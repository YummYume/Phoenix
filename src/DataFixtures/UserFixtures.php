<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $root = (new User())
            ->setEmail("root@phoenix.com")
            ->setPlainPassword("root")
            ->setRoles([User::ROLE_SUPER_ADMIN])
        ;

        $manager->persist($root);

        foreach (range(1, 20) as $i) {
            $user = (new User())
                ->setEmail("user$i@gmail.com")
                ->setPlainPassword('123456')
                ->setRoles([User::ROLE_USER])
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
