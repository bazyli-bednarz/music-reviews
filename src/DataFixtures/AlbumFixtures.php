<?php
/**
 * Album fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * Class AlbumFixtures.
 *
 * @codeCoverageIgnore
 */
class AlbumFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{

    /**
     * Get dependencies.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class, UserFixtures::class];
    }

    /**
     * Load album data.
     */
    protected function loadData(): void
    {
        $this->faker = Factory::create();

        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'albums', function (int $i) {
            $album = new Album();
            $album->setTitle(ucfirst($this->faker->words(3, true).' '.$this->faker->emoji));
            $album->setYear(intval($this->faker->year()));
            $album->setMark($this->faker->numberBetween(1, 5));
            $description = '**';
            $description .= $this->faker->paragraph(3);
            $description .= '**';
            $description .= "\n ## ".$this->faker->word."\n\n";
            $description .= $this->faker->paragraph(5);
            $description .= "\n ### ".$this->faker->word."\n\n";
            foreach (range(1, $this->faker->numberBetween(5, 15)) as $number) {
                $description .= $number.'. '.$this->faker->word.' ('.$this->faker->numberBetween(1, 10).':'.
                    str_pad($this->faker->numberBetween(0, 59), 2, '0', STR_PAD_LEFT)
                    .')'."\n\n";
            }
            $description .= $this->faker->paragraph(4);
            $description .= "\n ### ".$this->faker->word."\n\n";
            $description .= '>'.$this->faker->paragraph(2)."\n\n";
            $description .= $this->faker->paragraph(2);
            $album->setDescription($description);
            $album->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $album->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );

            /** @var User $author */
            $author = $this->getRandomReference('admins');
            $album->setAuthor($author);
            /** @var Category $category */
            $category = $this->getRandomReference('categories');
            $album->setCategory($category);
            /** @var Collection<Tag> $tags */
            $tags = $this->getRandomReferences('tags', $this->faker->numberBetween(0, 3));
            $tagsCount = count($tags);
            if ($tagsCount) {
                for ($i = 0; $i < $tagsCount; ++$i) {
                    $album->addTag($tags[$i]);
                }
            }
            /** @var Collection<Artist> $artists */
            $artists = $this->getRandomReferences('artists', $this->faker->numberBetween(1, 3));
            $artistsCount = count($artists);
            if ($artistsCount) {
                for ($i = 0; $i < $artistsCount; ++$i) {
                    $album->addArtist($artists[$i]);
                }
            }

            return $album;
        });

        $this->manager->flush();
    }
}
