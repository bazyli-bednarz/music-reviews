<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load comment data.
     */
    protected function loadData(): void
    {
        $this->faker = Factory::create();

        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(300, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setRating($this->faker->numberBetween(1, 5));
            $comment->setDescription($this->faker->paragraph(2).' '.$this->faker->emoji.' '.
            $this->faker->paragraph(2).' '.$this->faker->emoji);
            $comment->setCreatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            $comment->setUpdatedAt(
                \DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-100 days', '-1 days'))
            );
            /** @var Album $album */
            $album = $this->getRandomReference('albums');
            $comment->setAlbum($album);

            return $comment;
        });

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [AlbumFixtures::class];
    }
}
