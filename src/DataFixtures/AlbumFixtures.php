<?php
/**
 * Album fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Album;
use Faker\Factory;

/**
 * Class AlbumFixtures.
 */
class AlbumFixtures extends AbstractBaseFixtures
{
    /**
     * Load album data.
     */
    protected function loadData(): void
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 50; ++$i) {
            $album = new Album();
            $album->setTitle(ucfirst($this->faker->words(3, true).' '.$this->faker->emoji));
            $album->setYear(intval($this->faker->year()));
            $album->setMark($this->faker->numberBetween(1, 5));
            $album->setDescription($this->faker->paragraph(5));
            $album->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $album->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $this->manager->persist($album);
        }

        $this->manager->flush();
    }
}
