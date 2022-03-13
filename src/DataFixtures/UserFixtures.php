<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Root
        $root = (new User())
            ->setEmail('root@phoenix.com')
            ->setPlainPassword('root')
            ->setRoles([Role::SuperAdmin->value])
        ;

        $this->addReference(self::class.'root', $root);
        $manager->persist($root);

        // Users
        foreach (range(1, 20) as $i) {
            $user = (new User())
                ->setEmail("user$i@gmail.com")
                ->setPlainPassword('123456')
                ->setRoles([Role::User->value])
            ;

            $this->addReference(self::class."user$i", $user);
            $manager->persist($user);
        }

        // Responsibles
        foreach (range(1, 10) as $i) {
            $user = (new User())
                ->setEmail("responsible$i@gmail.com")
                ->setPlainPassword('123456')
                ->setRoles([Role::User->value])
            ;

            $this->addReference(self::class."responsible$i", $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
