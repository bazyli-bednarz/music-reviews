<?php
/**
 * Registration controller tests.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\AlbumRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class AlbumControllerTest.
 */
class SecurityControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/login';

    /**
     * Set up tests.
     */
    public function setUp(): void
    {
        $this->httpClient = static::createClient();
    }

    /**
     * Test register route.
     */
    public function testRegisterRoute(): void
    {
        // given
        $expectedCode = 302;
        $createEntityName = 'login@example.com';
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER'], $createEntityName, $createEntityName);
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        // when
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE);
        $form = $crawler->selectButton('Zaloguj')->form();

        $form['email'] = $createEntityName;
        $form['password'] = 'user1234';


        $this->httpClient->submit($form);

        // then
        $result = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedCode, $result);
    }

    /**
     * Test logout route.
     */
    public function testLogoutRoute(): void
    {
        // given
        $expectedCode = 302;
        $createEntityName = 'logout@example.com';
        $testUser = null;
        try {
            $testUser = $this->createUser(['ROLE_USER'], $createEntityName, $createEntityName);
        } catch (OptimisticLockException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
        }

        // when
        $this->httpClient->loginUser($testUser);
        $this->httpClient->request('GET', '/logout');

        // then
        $result = $this->httpClient->getResponse()->getStatusCode();
        $this->assertEquals($expectedCode, $result);
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
}


