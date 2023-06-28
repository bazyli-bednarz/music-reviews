<?php
/**
 * Category service tests.
 */

namespace App\Tests\Service;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\AlbumService;
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
class CategoryServiceTest extends KernelTestCase
{
    /**
     * Category repository.
     */
    private ?EntityManagerInterface $entityManager;

    /**
     * Category service.
     */
    private ?CategoryServiceInterface $categoryService;

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
        $this->categoryService = $container->get(CategoryService::class);
    }

    /**
     * Test save.
     *
     * @throws ORMException
     */
    public function testSave(): void
    {
        // given
        $expectedCategory = new Category();
        $expectedCategory->setTitle('Test category title');
        $expectedCategory->setDescription('Test category description');

        // when
        $this->categoryService->save($expectedCategory);

        // then
        $expectedCategoryId = $expectedCategory->getId();
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', $expectedCategoryId, Types::INTEGER)
            ->getQuery()
            ->getSingleResult();

        $this->assertEquals($expectedCategory, $resultCategory);
    }

    /**
     * Test delete.
     *
     * @throws ORMException
     */
    public function testDelete(): void
    {
        // given
        $categoryToDelete = new Category();
        $categoryToDelete->setTitle('Test category to delete');
        $categoryToDelete->setDescription('Test category description');
        $categoryToDelete->setCreatedAt(new \DateTimeImmutable('now'));
        $categoryToDelete->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->entityManager->persist($categoryToDelete);
        $this->entityManager->flush();
        $deletedCategoryId = $categoryToDelete->getId();

        // when
        $this->categoryService->delete($categoryToDelete);

        // then
        $resultCategory = $this->entityManager->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->where('category.id = :id')
            ->setParameter(':id', $deletedCategoryId, Types::INTEGER)
            ->getQuery()
            ->getOneOrNullResult();

        $this->assertNull($resultCategory);
    }

    /**
     * Test pagination list.
     */
    public function testGetPaginatedListForTwentyRecords(): void
    {
        // given
        $page = 1;
        $dataSetSize = 20;
        $expectedResultSize = CategoryRepository::PAGINATOR_ITEMS_PER_PAGE;

        $counter = 0;
        while ($counter < $dataSetSize) {
            $category = new Category();
            $category->setTitle('Test category title #'.$counter);
            $category->setDescription('Test category description');
            $category->setCreatedAt(new \DateTimeImmutable('now'));
            $category->setUpdatedAt(new \DateTimeImmutable('now'));
            $this->categoryService->save($category);

            ++$counter;
        }

        // when
        $result = $this->categoryService->getPaginatedList($page);

        // then
        $this->assertEquals($expectedResultSize, $result->count());
    }

    /**
     * Test if category without albums can be deleted.
     */
    public function testCanBeDeleted(): void
    {
        // given
        $categoryToDelete = new Category();
        $categoryToDelete->setTitle('Test category to delete');
        $categoryToDelete->setDescription('Test category description');
        $categoryToDelete->setCreatedAt(new \DateTimeImmutable('now'));
        $categoryToDelete->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->entityManager->persist($categoryToDelete);
        $this->entityManager->flush();

        // when
        $result = $this->categoryService->canBeDeleted($categoryToDelete);

        // then
        $this->assertEquals(true, $result);
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
     * Test if category with albums can be deleted.
     */
    public function testNotEmptyCanBeDeleted(): void
    {
        // given
        $categoryToDelete = new Category();
        $categoryToDelete->setTitle('Test category to delete when not empty');
        $categoryToDelete->setDescription('Test category description');
        $categoryToDelete->setCreatedAt(new \DateTimeImmutable('now'));
        $categoryToDelete->setUpdatedAt(new \DateTimeImmutable('now'));
        $this->entityManager->persist($categoryToDelete);

        $user = null;
        try {
            $user = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'admintest@example.com', 'Admin');
        } catch (ORMException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        $artist = $this->createArtist('Test artist');

        $album = $this->createAlbum($categoryToDelete, $artist, $user);

        $this->entityManager->persist($album);
        $this->entityManager->flush();

        // when
        $result = $this->categoryService->canBeDeleted($categoryToDelete);

        // then
        $this->assertEquals(false, $result);
    }
}
