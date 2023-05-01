<?php
/**
 * Hello controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class HelloControllerTest.
 */
class HelloControllerTest extends WebTestCase
{
    /**
     * Test '/hello' roure.
     */
    public function testHelloRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/hello');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }
}
