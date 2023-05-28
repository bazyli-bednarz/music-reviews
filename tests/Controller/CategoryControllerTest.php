<?php
/**
 * Categories controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CategoryControllerTest.
 */
class CategoryControllerTest extends WebTestCase
{
    /**
     * Test category index route.
     */
    public function testCategoryIndexRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/categories');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test category show route.
     *
     * @dataProvider dataProviderForTestCategoryShow
     */
    public function testCategoryShowRoute(string $input, int $expected): void
    {
        // given
        $client = static::createClient();
        $uri = '/categories/'.$input;

        // when
        $client->request('GET', $uri);
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expected, $resultHttpStatusCode);
    }

    /**
     * Data provider for test category show.
     */
    public function dataProviderForTestCategoryShow(): \Generator
    {
        yield 'Status code for id bad' => [
            'input' => 'bad',
            'expected' => 404,
        ];
    }

}


