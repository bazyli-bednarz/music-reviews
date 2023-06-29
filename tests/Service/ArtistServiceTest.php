<?php
/**
 * Category service tests.
 */

namespace App\Tests\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\AlbumService;
use App\Service\AlbumServiceInterface;
use App\Service\ArtistService;
use App\Service\ArtistServiceInterface;
use App\Service\CategoryService;
use App\Service\CategoryServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryServiceTest.
 */
class ArtistServiceTest extends KernelTestCase
{
    /**
     * Artist repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Artist service.
     */
    private ?ArtistServiceInterface $albumService;

    /**
     * Set up test.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        $this->albumService = $container->get(ArtistService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedArtist = new Artist();
        $expectedArtist->setName('Test artist save');
        $expectedArtist->setDescription('Test artist save');
        $expectedArtist->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedArtist->setUpdatedAt(new \DateTimeImmutable('now'));
        $expectedArtist->setSlug('test-artist-save');

        // when
        $this->albumService->save($expectedArtist);

        // then
        $expectedArtistId = $expectedArtist->getId();
        $resultArtist = $this->entityManager->createQueryBuilder()
            ->select('artist')
            ->from(Artist::class, 'artist')
            ->where('artist.id = :id')
            ->setParameter(':id', $expectedArtistId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedArtist, $resultArtist);
        $this->assertEquals($expectedArtist->getUpdatedAt(), $resultArtist->getUpdatedAt());
        $this->assertEquals($expectedArtist->getCreatedAt(), $resultArtist->getCreatedAt());
        $this->assertEquals($expectedArtist->getSlug(), $resultArtist->getSlug());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given

        $artist = $this->createArtist('Artist to delete');

        // when
        $this->entityManager->persist($artist);
        $this->entityManager->flush();
        $deletedArtistId = $artist->getId();

        // when
        $this->albumService->delete($artist);

        // then
        $resultArtist = $this->entityManager->createQueryBuilder()
            ->select('artist')
            ->from(Artist::class, 'artist')
            ->where('artist.id = :id')
            ->setParameter(':id', $deletedArtistId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultArtist);
    }


    /**
     * Test can be deleted.
     *
     * @throws ORMException
     */
    public function testCanBeDeleted(): void
    {
        // given

        $artist = $this->createArtist('Artist to check');
        $category = $this->createCategory('Category to check artist');
        $user = null;

        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'artisttocheck@example.com', 'usertocheckartist');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $album = $this->createAlbum($category, $artist, $user);

        // when
        $this->entityManager->persist($artist);
        $this->entityManager->flush();
        $deletedArtistId = $artist->getId();

        // when
        $result = $this->albumService->canBeDeleted($artist);

        // then
        $this->assertFalse($result);
    }

    /**
     * Test pagination list.
     */
    public function testGetPaginatedListForTwentyRecords(): void
    {
        // given
        $page = 1;
        $dataSetSize = 20;
        $expectedResultSize = ArtistRepository::PAGINATOR_ITEMS_PER_PAGE;

        $counter = 0;
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'artistpagination@example.pl', 'artistpagination');
        } catch (OptimisticLockException|ContainerExceptionInterface $e) {
        }
        while ($counter < $dataSetSize) {
            $artist = $this->createArtist('artistpagination'.$counter);
            $this->albumService->save($artist);
            ++$counter;
        }

        // when
        $result = $this->albumService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|ORMException|OptimisticLockException
     */
    protected function createUser(array $roles, string $email, string $username): User
    {
        $passwordHasher = static::getContainer()->get('security.password_hasher');
        $user = new User();
        $user->setBlocked(false);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                'user1234'
            )
        );
        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->save($user);

        return $user;
    }

    /**
     * Create artist.
     */
    private function createArtist(string $name): Artist
    {
        $author = new Artist();
        $author->setCreatedAt(new \DateTimeImmutable('now'));
        $author->setUpdatedAt(new \DateTimeImmutable('now'));
        $author->setName($name);
        $author->setDescription('Author test description');
        $artistRepository = self::getContainer()->get(ArtistRepository::class);
        $artistRepository->save($author);

        return $author;
    }

    /**
     * Create album.
     */
    private function createAlbum(Category $category, Artist $artist, User $user): Album
    {
        $album = new Album();
        $album->setCreatedAt(new \DateTimeImmutable('now'));
        $album->setUpdatedAt(new \DateTimeImmutable('now'));
        $album->setTitle('Album title');
        $album->setDescription('Album description');
        $album->setYear(1234);
        $album->setMark(4);
        $album->setCategory($category);
        $album->addArtist($artist);
        $album->setAuthor($user);

        $albumRepository = self::getContainer()->get(AlbumRepository::class);
        $albumRepository->save($album);

        return $album;
    }

    /**
     * Create category.
     */
    private function createCategory(string $title): Category
    {
        $category = new Category();
        $category->setCreatedAt(new \DateTimeImmutable('now'));
        $category->setUpdatedAt(new \DateTimeImmutable('now'));
        $category->setTitle($title);
        $category->setDescription('Category description');

        $categoryRepository = self::getContainer()->get(CategoryRepository::class);
        $categoryRepository->save($category);

        return $category;
    }
}
