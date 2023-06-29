<?php
/**
 * Album controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Cover;
use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\CoverRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AlbumControllerTest.
 */
class AlbumControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/albums';

    /**
     * Test album index route.
     */
    public function testAlbumIndexRoute(): void
    {
        // given

        // when
        $this->httpClient->request('GET', '/albums');
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test album show route.
     */
    public function testAlbumShowRoute(): void
    {
        // given
        $testArtist = $this->createArtist('Test Album Show');
        $testCategory = $this->createCategory('Test Album Show');
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testalbumshow@example.com', 'TestAlbumShow');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $expectedCode = 200;

        // when
        $testEntity = $this->createAlbum('Test Album Show', $testCategory, $testArtist, $testUser);
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testEntity->getSlug());
        $resultHttpStatusCode = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedCode, $resultHttpStatusCode);
    }

    /**
     * Test album comment.
     */
    public function testAlbumComment(): void
    {
        // given
        $testArtist = $this->createArtist('Test Album Comment');
        $testCategory = $this->createCategory('Test Album Comment');
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testalbumcomment@example.com', 'TestAlbumComment');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $expectedCode = 302;

        // when
        $this->httpClient->loginUser($testUser);

        $testEntity = $this->createAlbum('Test Album Show', $testCategory, $testArtist, $testUser);
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testEntity->getSlug());

        $form = $crawler->selectButton('Skomentuj')->form();
        $form['comment[description]'] = '## Comment description';
        $form['comment[rating]'] = 1;

        $this->httpClient->submit($form);
        $result = $this->httpClient->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expectedCode, $result);

    }

    /**
     * Test album comment when user is blocked.
     */
    public function testAlbumCommentWhenUserBlocked(): void
    {
        // given
        $testArtist = $this->createArtist('Test Album Comment Blocked');
        $testCategory = $this->createCategory('Test Album Comment Blocked');
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testalbumcommentblocked@example.com', 'TestAlbumCommentBlocked');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        $testUserBlocked = null;
        try {
            $testUserBlocked = $this->createUser(['ROLE_USER'], 'testalbumcommentuserblocked@example.com', 'TestAlbumCommentUserBlocked');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        // when
        $this->httpClient->loginUser($testUser);

        $testEntity = $this->createAlbum('Test Album Show', $testCategory, $testArtist, $testUser);



        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$testEntity->getSlug());

        $testUser->setBlocked(true);
        $this->httpClient->loginUser($testUserBlocked);
        $form = $crawler->selectButton('Skomentuj')->form();
        $form['comment[description]'] = '## Comment wont send';
        $form['comment[rating]'] = 1;

        $this->httpClient->submit($form);
        $entityRepository = static::getContainer()->get(CommentRepository::class);

        // then
        $savedEntity = $entityRepository->findOneByDescription('## Comment wont send');

        // then
        $this->assertNull($savedEntity);

    }

    /**
     * Test album create route.
     */
    public function testAlbumCreateRoute(): void
    {
        // given
        $testArtist = $this->createArtist('Test Album Create');
        $testCategory = $this->createCategory('Test Album Create');
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testalbumcreate@example.com', 'TestAlbumShow');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $expectedCode = 302;
        $this->httpClient->loginUser($testUser);


        $createEntityName = 'createAlbum';

        // when
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/create');
        $form = $crawler->selectButton('Zapisz')->form();
        $form['album[title]'] = $createEntityName;
        $form['album[description]'] = $createEntityName;
        $form['album[artists]'] = 1;
        $form['album[category]'] = '1';
        $form['album[year]'] = 1999;
        $form['album[mark]'] = 1;
        $form['album[tags]'] = '';
        $form['album[file]'] = new UploadedFile(__DIR__.'/../fixtures/dummy.png', 'dummy.png', null, UPLOAD_ERR_OK, true);

        $this->httpClient->submit($form);
        $entityRepository = static::getContainer()->get(AlbumRepository::class);

        // then
        $savedEntity = $entityRepository->findOneByTitle($createEntityName);

        $this->assertEquals($createEntityName, $savedEntity->getTitle());

        $result = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedCode, $result);
    }

    /**
     * Test album edit route.
     */
    public function testAlbumEditRoute(): void
    {
        // given
        $testArtist = $this->createArtist('Test Album Edit');
        $testCategory = $this->createCategory('Test Album Edit');
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testalbumedit@example.com', 'TestAlbumEdit');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $expectedCode = 302;
        $this->httpClient->loginUser($testUser);


        $createEntityName = 'editAlbum';
        $album = $this->createAlbum($createEntityName, $testCategory, $testArtist, $testUser);
        $oldAlbumSlug = $album->getSlug();

        // when
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$oldAlbumSlug.'/edit');
        $form = $crawler->selectButton('Edytuj')->form();
        $form['album[title]'] = $createEntityName;
        $form['album[description]'] = $createEntityName;
        $form['album[artists]'] = 1;
        $form['album[category]'] = '1';
        $form['album[year]'] = 1999;
        $form['album[mark]'] = 1;
        $form['album[tags]'] = '';
        $form['album[file]'] = new UploadedFile(__DIR__.'/../fixtures/dummy.png', 'dummy.png', null, UPLOAD_ERR_OK, true);

        $this->httpClient->submit($form);
        $entityRepository = static::getContainer()->get(AlbumRepository::class);

        // then
        $savedEntity = $entityRepository->findOneBySlug($createEntityName);


        $this->assertEquals($createEntityName,
            $savedEntity->getTitle());

        $result = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedCode, $result);
    }

    /**
     * Test album delete route.
     */
    public function testCategoryDeleteRoute(): void
    {
        // given
        $testArtist = $this->createArtist('Test Album Delete');
        $testCategory = $this->createCategory('Test Album Delete');
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER', 'ROLE_ADMIN'], 'testalbumdelete@example.com', 'TestAlbumDelete');
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }
        $expectedCode = 302;
        $this->httpClient->loginUser($testUser);


        $createEntityName = 'editAlbum';
        $album = $this->createAlbum($createEntityName, $testCategory, $testArtist, $testUser);
        $oldAlbumSlug = $album->getSlug();
        $oldAlbumId = $album->getId();
        $albumRepository = static::getContainer()->get(AlbumRepository::class);

        // when
        $this->httpClient->request('GET', self::TEST_ROUTE.'/'.$oldAlbumSlug.'/delete');

        // when
        $this->httpClient->submitForm(
            'Usuń',
        );

        // then
        $this->assertNull($albumRepository->findOneById($oldAlbumId));
    }


    /**
     * Create user.
     *
     * @param array $roles User roles
     *
     * @return User User entity'
     *
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|OptimisticLockException
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
     *
     * @param string $title
     * @param Category $category
     * @param Artist $artist
     * @param User $user
     *
     * @return Album
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

    /**
     * Create artist.
     *
     * @param string $name
     *
     * @return Artist
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


