<?php
/**
 * Artist fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Artist;

/**
 * Class ArtistFixtures.
 *
 * @codeCoverageIgnore
 */
class ArtistFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        $this->createMany(20, 'artists', function (int $i) {
            $artist = new Artist();
            $artist->setName($this->faker->unique()->name);
            $artist->setDescription($this->faker->realText);
            $artist->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $artist->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            return $artist;
        });

        $this->manager->flush();
    }
}
