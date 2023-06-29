<?php
/**
 * Comment service tests.
 */

namespace App\Tests\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\AlbumService;
use App\Service\CommentService;
use App\Service\CommentServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CommentServiceTest.
 */
class CommentServiceTest extends KernelTestCase
{
    /**
     * Comment repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Comment service.
     */
    private ?CommentServiceInterface $commentService;

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
        $this->commentService = $container->get(CommentService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedComment = new Comment();
        $expectedComment->setDescription('Test comment description');
        $expectedComment->setRating(5);

        $category = $this->createCategory('comment category');
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER'], 'commentuser@example.com', 'commentuser');
        } catch (OptimisticLockException|ContainerExceptionInterface $e) {
        }
        $artist = $this->createArtist('comment artist');
        $album = $this->createAlbum($category, $artist, $user);

        $expectedComment->setAlbum($album);
        $expectedComment->setAuthor($user);

        // when
        $this->commentService->save($expectedComment);

        // then
        $expectedCommentId = $expectedComment->getId();
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment')
            ->from(Comment::class, 'comment')
            ->where('comment.id = :id')
            ->setParameter(':id', $expectedCommentId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedComment, $resultComment);
        $this->assertEquals($expectedComment->getUpdatedAt(), $resultComment->getUpdatedAt());
        $this->assertEquals($expectedComment->getCreatedAt(), $resultComment->getCreatedAt());
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $commentToDelete = new Comment();
        $commentToDelete->setDescription('Test comment description');
        $commentToDelete->setRating(5);
        $commentToDelete->setCreatedAt(new \DateTimeImmutable('now'));
        $commentToDelete->setUpdatedAt(new \DateTimeImmutable('now'));

        $deletedCommentId = $commentToDelete->getId();

        $category = $this->createCategory('comment category delete');
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER'], 'commentuserdelete@example.com', 'commentuser');
        } catch (OptimisticLockException|ContainerExceptionInterface $e) {
        }
        $artist = $this->createArtist('comment artist delete');
        $album = $this->createAlbum($category, $artist, $user);

        $commentToDelete->setAlbum($album);
        $commentToDelete->setAuthor($user);

        $this->entityManager->persist($commentToDelete);
        $this->entityManager->flush();

        // when
        $this->commentService->delete($commentToDelete);

        // then
        $resultComment = $this->entityManager->createQueryBuilder()
            ->select('comment')
            ->from(Comment::class, 'comment')
            ->where('comment.id = :id')
            ->setParameter(':id', $deletedCommentId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultComment);
    }

    /**
     * Test pagination list.
     */
    public function testGetPaginatedListForTwentyRecords(): void
    {
        // given
        $page = 1;
        $dataSetSize = 20;
        $expectedResultSize = CommentRepository::PAGINATOR_ITEMS_PER_PAGE;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $comment = new Comment();
            $comment->setDescription('Test comment description');
            $comment->setRating(5);
            $comment->setCreatedAt(new \DateTimeImmutable('now'));
            $comment->setUpdatedAt(new \DateTimeImmutable('now'));

            $category = $this->createCategory('comment category pagination #'.$counter);
            $user = null;
            try {
                $user = $this->createUser(['ROLE_USER'], 'commentuserpagination@example.com'.$counter, 'commentuser');
            } catch (OptimisticLockException|ContainerExceptionInterface $e) {
            }
            $artist = $this->createArtist('comment artist pagination #'.$counter);
            $album = $this->createAlbum($category, $artist, $user);

            $comment->setAlbum($album);
            $comment->setAuthor($user);

            $this->commentService->save($comment);

            ++$counter;
        }

        // when
        $result = $this->commentService->getPaginatedList($page);

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
     * Create test category.
     *
     * @param string $title
     *
     * @return Category
     */
    private function createCategory(string $title): Category
    {
        $category = new Category();
        $category->setTitle($title);
        $category->setDescription('Create category description');
        $categoryRepository = self::getContainer()->get(CategoryRepository::class);
        $categoryRepository->save($category);

        return $category;
    }

}
