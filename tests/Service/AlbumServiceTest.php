<?php
/**
 * Category service tests.
 */

namespace App\Tests\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Cover;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\CoverRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Service\AlbumService;
use App\Service\AlbumServiceInterface;
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
class AlbumServiceTest extends KernelTestCase
{
    /**
     * Album repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Album service.
     */
    private ?AlbumServiceInterface $albumService;

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
        $this->albumService = $container->get(AlbumService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedAlbum = new Album();
        $expectedAlbum->setTitle('Test album title');
        $expectedAlbum->setSlug('test-album-title');
        $expectedAlbum->setDescription('Test album description');
        try {
            $expectedAlbum->setAuthor($this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admin2@example.pl', 'Admin test'));
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
        }

        $expectedAlbum->setMark(4);
        $expectedAlbum->setCategory($this->createCategory('Title'));
        $expectedAlbum->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedAlbum->setUpdatedAt(new \DateTimeImmutable('now'));
        $expectedAlbum->setYear(1234);

        $artist = $this->createArtist('Artist name for album test');
        $expectedAlbum->addArtist($artist);
        $expectedAlbum->removeArtist($artist);

        $tag = new Tag();
        $tag->setTitle('album-tag');
        $expectedAlbum->addTag($tag);
        $expectedAlbum->removeTag($tag);

        $cover = $this->createCover('dummy.png', $expectedAlbum);
        $expectedAlbum->setCover($cover);

        $secondAlbum = null;
        try {
            $secondAlbum = $this->createAlbum($this->createCategory('Title-2'), $artist, $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admin2-double@example.pl', 'Admin test'));
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        $cover->setAlbum($secondAlbum);
        $expectedAlbum->setCover($cover);


        // when
        $this->albumService->save($expectedAlbum);

        // then
        $expectedAlbumId = $expectedAlbum->getId();
        $resultAlbum = $this->entityManager->createQueryBuilder()
            ->select('album')
            ->from(Album::class, 'album')
            ->where('album.id = :id')
            ->setParameter(':id', $expectedAlbumId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedAlbum, $resultAlbum);
        $this->assertEquals($expectedAlbum->getUpdatedAt(), $resultAlbum->getUpdatedAt());
        $this->assertEquals($expectedAlbum->getCreatedAt(), $resultAlbum->getCreatedAt());
        $this->assertEquals($expectedAlbum->getSlug(), $resultAlbum->getSlug());
        $this->assertEquals($expectedAlbum->getAuthor(), $resultAlbum->getAuthor());
        $this->assertEquals($expectedAlbum->getCover()->getId(), $resultAlbum->getCover()->getId());
    }

    /**
     * Test count comments.
     *
     * @throws ORMException
     */
    public function testCountComments(): void
    {
        // given
        $expectedAlbum = new Album();
        $expectedAlbum->setTitle('Test album comments title');
        $expectedAlbum->setDescription('Test album description');
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'adminalbumcomments@example.pl', 'Admin test');
        } catch (OptimisticLockException|ContainerExceptionInterface $e) {
        }
        $expectedAlbum->setAuthor($user);


        $expectedAlbum->setMark(4);
        $expectedAlbum->setCategory($this->createCategory('Title category albums comments'));
        $expectedAlbum->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedAlbum->setUpdatedAt(new \DateTimeImmutable('now'));
        $expectedAlbum->setYear(1234);

        $artist = $this->createArtist('Artist name for album comments test');
        $expectedAlbum->addArtist($artist);


        // when
        $this->albumService->save($expectedAlbum);

        // then
        $comments = $this->albumService->countComments($expectedAlbum);
        $this->assertEquals(0, $comments);

        $comment = new Comment();
        $comment->setDescription('Comment testCountComments');
        $comment->setRating(5);
        $comment->setAlbum($expectedAlbum);
        $comment->setAuthor($user);

        $this->assertEquals($expectedAlbum, $comment->getAlbum());
        $this->assertEquals($user, $comment->getAuthor());

    }


    /**
     * Test prepare filters.
     *
     * @throws ORMException
     */
    public function testPrepareFilters(): void
    {
        // given
        $expectedAlbum = new Album();
        $expectedAlbum->setTitle('Test album tags title');
        $expectedAlbum->setDescription('Test album description');
        try {
            $expectedAlbum->setAuthor($this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'adminalbumfilters@example.pl', 'Admin test'));
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
        }

        $expectedAlbum->setMark(4);
        $expectedAlbum->setCategory($this->createCategory('Title category albums filters'));
        $expectedAlbum->setCreatedAt(new \DateTimeImmutable('now'));
        $expectedAlbum->setUpdatedAt(new \DateTimeImmutable('now'));
        $expectedAlbum->setYear(1234);

        $artist = $this->createArtist('Artist name for album filters test');
        $expectedAlbum->addArtist($artist);

        $tag = new Tag();
        $tag->setTitle('Tag');
        $expectedTags = ['tag' => $tag];
        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $tagId = $tag->getId();



        // when
        $resultTags = $this->albumService->prepareFilters(['tag_slug' => 'tag']);
        $tagRepository = static::getContainer()->get(TagRepository::class);
        $tagInDb = $tagRepository->findOneById($tagId);

        // then
        $this->assertEquals($expectedTags, $resultTags);
        $this->assertEquals($tagId, $tagInDb->getId());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $albumToDelete = new Album();
        $albumToDelete->setTitle('Test album title to delete');
        $albumToDelete->setDescription('Test album description');
        try {
            $albumToDelete->setAuthor($this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admin3@example.pl', 'Admin test'));
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
        }

        $albumToDelete->setMark(4);
        $albumToDelete->setCategory($this->createCategory('testDelete category title'));
        $albumToDelete->setCreatedAt(new \DateTimeImmutable('now'));
        $albumToDelete->setUpdatedAt(new \DateTimeImmutable('now'));
        $albumToDelete->setYear(1234);

        $artist = $this->createArtist('Artist name for album test delete');
        $albumToDelete->addArtist($artist);


        // when
        $this->entityManager->persist($albumToDelete);
        $this->entityManager->flush();
        $deletedAlbumId = $albumToDelete->getId();

        // when
        $this->albumService->delete($albumToDelete);

        // then
        $resultAlbum = $this->entityManager->createQueryBuilder()
            ->select('album')
            ->from(Album::class, 'album')
            ->where('album.id = :id')
            ->setParameter(':id', $deletedAlbumId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultAlbum);
    }

    /**
     * Test pagination list.
     */
    public function testGetPaginatedListForTwentyRecords(): void
    {
        // given
        $page = 1;
        $dataSetSize = 20;
        $expectedResultSize = AlbumRepository::PAGINATOR_ITEMS_PER_PAGE;

        $counter = 0;
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admin4@example.pl', 'Admin album'.$counter);
        } catch (OptimisticLockException|ContainerExceptionInterface|ORMException $e) {
        }
        while ($counter < $dataSetSize) {
            $album = new Album();
            $album->setTitle('Test album title paginated');
            $album->setDescription('Test album description');
            try {
                $album->setAuthor($user);
            } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
            }

            $album->setMark(3);
            $album->setCategory($this->createCategory('testDelete category title'.$counter));
            $album->setCreatedAt(new \DateTimeImmutable('now'));
            $album->setUpdatedAt(new \DateTimeImmutable('now'));
            $album->setYear(1123);

            $artist = $this->createArtist('Artist name for album pagination test'.$counter);
            $album->addArtist($artist);

            $this->albumService->save($album);

            ++$counter;
        }

        // when
        $result = $this->albumService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test pagination list.
     */
    public function testGetListByCategory(): void
    {
        // given
        $page = 1;
        $dataSetSize = 20;
        $expectedResultSize = AlbumRepository::PAGINATOR_ITEMS_PER_PAGE;

        $counter = 0;
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admin5@example.pl', 'Admin album');
        } catch (OptimisticLockException|ContainerExceptionInterface|ORMException $e) {
        }
        $category = $this->createCategory('testGetListByCategory category title');
        while ($counter < $dataSetSize) {
            $album = new Album();
            $album->setTitle('Test album title paginated by category');
            $album->setDescription('Test album description');

            try {
                $album->setAuthor($user);
            } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
            }

            $album->setMark(3);
            $album->setCategory($category);
            $album->setCreatedAt(new \DateTimeImmutable('now'));
            $album->setUpdatedAt(new \DateTimeImmutable('now'));
            $album->setYear(1123);

            $artist = $this->createArtist('Artist name for album pagination by category test'.$counter);
            $album->addArtist($artist);

            $this->albumService->save($album);

            ++$counter;
        }

        // when
        $result = $this->albumService->getPaginatedListByCategory($category, $page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test pagination list.
     */
    public function testGetListByArtist(): void
    {
        // given
        $page = 1;
        $dataSetSize = 5;
        $expectedResultSize = 5;

        $counter = 0;
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admin6@example.pl', 'Admin album');
        } catch (OptimisticLockException|ContainerExceptionInterface|ORMException $e) {
        }
        $category = $this->createCategory('testGetListByArtist category title');
        $artist = $this->createArtist('Artist name for album pagination by artist test');
        while ($counter < $dataSetSize) {
            $album = new Album();
            $album->setTitle('Test album title paginated by artist');
            $album->setDescription('Test album description');

            try {
                $album->setAuthor($user);
            } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface|ORMException $e) {
            }

            $album->setMark(3);
            $album->setCategory($category);
            $album->setCreatedAt(new \DateTimeImmutable('now'));
            $album->setUpdatedAt(new \DateTimeImmutable('now'));
            $album->setYear(1123);

            $album->addArtist($artist);

            $this->albumService->save($album);

            ++$counter;
        }

        // when
        $result = $this->albumService->getPaginatedListByArtist($artist, $page);

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


    /**
     * Create cover.
     *
     * @param string $filename
     * @param Album $album
     * @return Cover
     */
    public function createCover(string $filename, Album $album): Cover
    {
        $cover = new Cover();
        $cover->setFilename($filename);
        $cover->setAlbum($album);

        $coverRepository = self::getContainer()->get(CoverRepository::class);
        $coverRepository->save($cover);

        return $cover;
    }
}
