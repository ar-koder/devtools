<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Todo;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TodoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 200; ++$i) {
            /** @var User $user */
            $user = $this->getReference(sprintf('%s-%s', UserFixtures::REFERENCE, $faker->numberBetween(0, 9)));
            $post = (new Todo())
                ->setTitle($faker->realText())
                ->setUser($user)
                ->setCompleted($faker->boolean())
                ;
            $manager->persist($post);
            $manager->flush();
        }
    }

    /**
     * @return array<class-string<UserFixtures>>
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
