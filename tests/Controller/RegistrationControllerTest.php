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
class RegistrationControllerTest extends WebTestCase
{
    /**
     * Test route.
     *
     * @const string
     */
    public const TEST_ROUTE = '/register';

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
        $createEntityName = 'register@example.com';

        // when
        $crawler = $this->httpClient->request('GET', self::TEST_ROUTE);
        $form = $crawler->selectButton('Zarejestruj się')->form();

        $form['registration_form[email]'] = 'register@example.com';
        $form['registration_form[username]'] = 'register';
        $form['registration_form[password]'] = 'password';
        $form['registration_form[agreeTerms]']->tick();

        $this->httpClient->submit($form);
        $entityRepository = static::getContainer()->get(UserRepository::class);

        // then
        $savedEntity = $entityRepository->findOneByEmail($createEntityName);

        $this->assertEquals($createEntityName, $savedEntity->getEmail());

        $result = $this->httpClient->getResponse()->getStatusCode();

        $this->assertEquals($expectedCode, $result);
    }
}


