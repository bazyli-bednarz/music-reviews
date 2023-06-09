<?php
/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

/**
 * Class CommentFixtures.
 *
 * @codeCoverageIgnore
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Get dependencies.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [AlbumFixtures::class, UserFixtures::class];
    }

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

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $comment->setAuthor($author);

            return $comment;
        });

        $this->manager->flush();
    }
}
