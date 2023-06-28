<?php
/**
 * Categories controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Tests\BaseFunctions;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/categories';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test category index route.
     */
    public function testCategoryIndexRoute(): void
    {
        // when
        $this->httpClient->request('GET', self::TEST_ROUTE);
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test category show route.
     *
     * @dataProvider dataProviderForTestCategoryShow
     */
    public function testCategoryShowRoute(string $input, int $expectedCode): void
    {
        // given
        $expectedCategory = new Category();
        $expectedCategory->setTitle($input);
        $expectedCategory->setDescription('Test category description');
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);

        // when
        $categoryRepository->save($expectedCategory);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$expectedCategory->getSlug());
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedCode, $resultHttpStatusCode);
    }

    /**
     * Data provider for test category show.
     */
    public function dataProviderForTestCategoryShow(): \Generator
    {
        yield 'Status code' => [
            'input' => 'Test Category Controller Show',
            'expected' => 200,
        ];
    }

    /**
     * Test category create route.
     */
    public function testCategoryCreateRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                ['ROLE_USER', 'ROLE_ADMIN'],
                'testCatCreate@example.com',
                'testCategoryCreate'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface $e) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $createCategoryName = 'createCategoryName';

        // when
        $this->httpClient->submitForm(
            'Zapisz',
            ['category' => ['title' => $createCategoryName, 'description' => 'Test']]
        );

        // then
        $savedCategory = $categoryRepository->findOneByTitle($createCategoryName);
        $this->assertEquals($createCategoryName,
            $savedCategory->getTitle());

        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
    }

    /**
     * Test category edit route.
     */
    public function testCategoryEditRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                ['ROLE_USER', 'ROLE_ADMIN'],
                'testCatEdit@example.com',
                'testCategoryEdit'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface $e) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $editCategoryName = 'editCategoryName';
        $testCategory->setTitle($editCategoryName);
        $testCategory->setDescription('Test');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);
        $testCategorySlug = $testCategory->getSlug();
        $testCategoryId = $testCategory->getId();
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testCategorySlug.'/edit');


        // when
        $this->httpClient->submitForm(
            'Edytuj',
            ['category' => ['title' => $editCategoryName, 'description' => 'Test']]
        );

        // then
        $savedCategory = $categoryRepository->findOneBySlug($testCategorySlug);
        $this->assertEquals($testCategorySlug,
            $savedCategory->getSlug());

        $result = $this->httpClient->getResponse();
        $this->assertEquals(302, $result->getStatusCode());
    }

    /**
     * Test category delete route.
     */
    public function testCategoryDeleteRoute(): void
    {
        // given
        $user = null;
        try {
            $user = $this->createUser(
                ['ROLE_USER', 'ROLE_ADMIN'],
                'testCatDelete@example.com',
                'testCategoryDelete'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface $e) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $testCategory->setTitle('deleteCategoryName');
        $testCategory->setDescription('Test');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));
        $categoryRepository->save($testCategory);
        $testCategorySlug = $testCategory->getSlug();
        $testCategoryId = $testCategory->getId();

        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testCategorySlug.'/delete');

        // when
        $this->httpClient->submitForm(
            'Usuń',
        );

        // then
        $this->assertNull($categoryRepository->findOneById($testCategoryId));
    }

    /**
     * Test category delete route.
     */
    public function testCategoryDeleteWhenNotEmptyRoute(): void
    {
        // given
        $this->expectException(InvalidArgumentException::class);

        $user = null;
        try {
            $user = $this->createUser(
                ['ROLE_USER', 'ROLE_ADMIN'],
                'testCatDeleteNE@example.com',
                'testCategoryDelete'
            );
        } catch (OptimisticLockException|NotFoundExceptionInterface|ORMException|ContainerExceptionInterface $e) {
        }

        $this->httpClient->loginUser($user);
        $categoryRepository = static::getContainer()->get(CategoryRepository::class);
        $testCategory = new Category();
        $testCategory->setTitle('deleteCategoryName');
        $testCategory->setDescription('Test');
        $testCategory->setCreatedAt(new \DateTimeImmutable('now'));
        $testCategory->setUpdatedAt(new \DateTimeImmutable('now'));


        $artist = $this->createArtist('DeleteNE');
        $album = $this->createAlbum('DeleteNEAlbum', $testCategory, $artist, $user);

        dump($album);

        $categoryRepository->save($testCategory);
        $testCategorySlug = $testCategory->getSlug();
        $testCategoryId = $testCategory->getId();



        // when
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testCategorySlug.'/delete');

        // then
        $this->assertNotNull($categoryRepository->findOneById($testCategoryId));

    }

    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity'
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
     * Create album.
     */
    public function createAlbum(string $title, Category $category, Artist $artist, User $user): Album
    {
        $album = new Album();
        $album->setCreatedAt(new \DateTimeImmutable('now'));
        $album->setUpdatedAt(new \DateTimeImmutable('now'));
        $album->setTitle($title);
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
}
