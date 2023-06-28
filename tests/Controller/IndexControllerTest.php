<?php
/**
 * Index controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class IndexControllerTest.
 */
class IndexControllerTest extends WebTestCase
{
    /**
     * Test index route.
     */
    public function testIndexRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }
}
