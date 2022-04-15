<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AlbumFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'album';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 100; ++$i) {
            /** @var User $user */
            $user = $this->getReference(sprintf('%s-%s', UserFixtures::REFERENCE, $faker->numberBetween(0, 9)));
            $post = (new Album())
                ->setTitle($faker->realText())
                ->setUser($user)
                ;
            $manager->persist($post);
            $manager->flush();
            $this->addReference(sprintf('%s-%s', self::REFERENCE, $i), $post);
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
