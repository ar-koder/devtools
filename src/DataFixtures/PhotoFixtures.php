<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Photo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PhotoFixtures extends Fixture implements DependentFixtureInterface
{
    public const REFERENCE = 'photo';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 2500; ++$i) {
            /** @var Album $album */
            $album = $this->getReference(sprintf('%s-%s', AlbumFixtures::REFERENCE, $faker->numberBetween(0, 99)));
            $post = (new Photo())
                ->setAlbum($album)
                ->setTitle($faker->realText())
                ->setUrl($faker->imageUrl())
                ->setThumbnailUrl($faker->imageUrl(150, 150))
                ;
            $manager->persist($post);
            $manager->flush();
            $this->addReference(sprintf('%s-%s', self::REFERENCE, $i), $post);
        }
    }

    /**
     * @return array<class-string<AlbumFixtures>>
     */
    public function getDependencies(): array
    {
        return [
            AlbumFixtures::class,
        ];
    }
}
