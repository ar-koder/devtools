<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public const REFERENCE = 'user';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; ++$i) {
            $user = (new User())
                ->setEmail($faker->email)
                ->setName($faker->name)
                ->setPhone($faker->phoneNumber)
                ->setWebsite($faker->domainName)
            ;
            $manager->persist($user);
            $manager->flush();
            $this->addReference(sprintf('%s-%s', self::REFERENCE, $i), $user);
        }
    }
}
