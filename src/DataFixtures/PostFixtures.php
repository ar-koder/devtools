<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var string
     */
    public const REFERENCE = 'post';

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 100; ++$i) {
            /** @var User $user */
            $user = $this->getReference(sprintf('%s-%s', UserFixtures::REFERENCE, $faker->numberBetween(0, 9)));
            $post = (new Post())
                ->setTitle($faker->realText())
                ->setBody($faker->realText(800))
                ->setUser($user)
                ;
            $manager->persist($post);
            $manager->flush();
            $this->addReference(sprintf('%s-%s', self::REFERENCE, $i), $post);
        }
    }

    /**
     * @return array<class-string<\App\DataFixtures\UserFixtures>>
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
