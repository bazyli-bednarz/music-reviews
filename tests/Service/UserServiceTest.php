<?php
/**
 * User service tests.
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
use App\Service\UserService;
use App\Service\UserServiceInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoryServiceTest.
 */
class UserServiceTest extends KernelTestCase
{
    /**
     * User repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * User service.
     */
    private ?UserServiceInterface $userService;

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
        $this->userService = $container->get(UserService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedUser = new User();
        $expectedUser->setUsername('Test user save');
        $expectedUser->setEmail('testusersave@example.com');
        $expectedUser->setPassword('user1234');

        // when
        $this->userService->save($expectedUser);
        $this->userService->upgradePassword($expectedUser, 'user1234');
        $expectedUser->setSlug('test-slug');

        // then
        $expectedUserId = $expectedUser->getId();
        $resultUser = $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->where('user.id = :id')
            ->setParameter(':id', $expectedUserId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedUser, $resultUser);
    }

    /**
     * Test delete.
     *
     * @throws NonUniqueResultException|ORMException
     */
    public function testDelete(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER'], 'usertodelete@example.com', 'User to delete');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        // when
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $deletedUserId = $user->getId();

        // when
        $this->userService->delete($user);

        // then
        $resultUser = $this->entityManager->createQueryBuilder()
            ->select('user')
            ->from(User::class, 'user')
            ->where('user.id = :id')
            ->setParameter(':id', $deletedUserId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultUser);
    }


    /**
     * Test can be deleted.
     *
     * @throws ORMException
     */
    public function testCanBeDeleted(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER'], 'usercanbedeleted@example.com', 'User to delete');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        // when
        $result = $this->userService->canBeDeleted($user);

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
        $expectedResultSize = UserRepository::PAGINATOR_ITEMS_PER_PAGE;

        $counter = 0;
        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'userpagination@example.pl', 'userpagination');
        } catch (OptimisticLockException|ContainerExceptionInterface $e) {
        }
        while ($counter < $dataSetSize) {
            $user = $this->createUser(['ROLE_USER'], 'userpaginationtest@example.com'.$counter, 'userpagination'.$counter);
            $this->userService->save($user);
            ++$counter;
        }

        // when
        $result = $this->userService->getPaginatedList($page);

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
