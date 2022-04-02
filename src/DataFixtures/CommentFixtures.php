<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 1000; ++$i) {
            /** @var Post $post */
            $post = $this->getReference(sprintf('%s-%s', PostFixtures::REFERENCE, $faker->numberBetween(0, 99)));
            $comment = (new Comment())
                ->setTitle($faker->realText(50))
                ->setBody($faker->realText())
                ->setEmail($faker->email)
                ->setPost($post)
            ;
            $manager->persist($comment);
        }

        $manager->flush();
    }

    /**
     * @return array<class-string<PostFixtures>>
     */
    public function getDependencies(): array
    {
        return [
            PostFixtures::class,
        ];
    }
}
