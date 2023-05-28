<?php
/**
 * Album fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * Class AlbumFixtures.
 */
class AlbumFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
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
            $album->setDescription($this->faker->paragraph(5));
            $album->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $album->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
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

            return $album;
        });

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class, TagFixtures::class];
    }
}
