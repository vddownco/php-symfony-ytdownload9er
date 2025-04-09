<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     * @return array<string, string>
     */
    public static function getGroups(): array
    {
        return ['user', 'all'];
    }

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $items = [
            [
                'email'    => 'admin@admin.local',
                'password' => 'admin123456',
                'roles'    => ['ROLE_ADMIN'],
            ],
        ];

        foreach ($items as $item) {
            $user = new User();
            $user
                ->setEmail($item['email'])
                ->setPassword($this->passwordHasher->hashPassword(
                    $user,
                    $item['password'])
                )
                ->setRoles($item['roles'])
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
