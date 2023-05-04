<?php
/**
 * Album controller tests.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AlbumControllerTest.
 */
class AlbumControllerTest extends WebTestCase
{
    /**
     * Test album index route.
     */
    public function testAlbumIndexRoute(): void
    {
        // given
        $client = static::createClient();

        // when
        $client->request('GET', '/albums');
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals(200, $resultHttpStatusCode);
    }

    /**
     * Test album show route.
     *
     * @dataProvider dataProviderForTestAlbumShow
     */
    public function testAlbumShowRoute(string $input, int $expected): void
    {
        // given
        $client = static::createClient();
        $uri = '/albums/'.$input;

        // when
        $client->request('GET', $uri);
        $resultHttpStatusCode = $client->getResponse()->getStatusCode();

        // then
        $this->assertEquals($expected, $resultHttpStatusCode);
    }

    /**
     * Data provider for test album show.
     */
    public function dataProviderForTestAlbumShow(): \Generator
    {
        yield 'Status code for id 1' => [
            'input' => '1',
            'expected' => 200,
        ];
        yield 'Status code for id 2' => [
            'input' => '2',
            'expected' => 200,
        ];
        yield 'Status code for id 3' => [
            'input' => '3',
            'expected' => 200,
        ];
        yield 'Status code for id invalid' => [
            'input' => 'invalid',
            'expected' => 404,
        ];
        yield 'Status code for id bad' => [
            'input' => 'bad',
            'expected' => 404,
        ];
    }

}


