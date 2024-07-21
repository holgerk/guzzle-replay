<?php

namespace Holgerk\GuzzleReplay\Tests\examples;

use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\GuzzleReplay;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class SimpleApiTest extends TestCase
{
    public function testGetUuid(): void
    {
        // GIVEN
        $client = new Client();
        $middleware = GuzzleReplay::create(GuzzleReplay::MODE_REPLAY);
        $middleware->inject($client);

        // WHEN
        $api = new SimpleApi($client);
        $uuid = $api->getUuid();

        // THEN
        assertEquals('e05cdd05-879d-45bc-94f2-ddefa584822f', $uuid);
    }

    public static function guzzleRecording_testGetUuid(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://httpbin.org/uuid',
                            'headers' => [
                                'User-Agent' => [
                                    'GuzzleHttp/7',
                                ],
                                'Host' => [
                                    'httpbin.org',
                                ],
                            ],
                            'body' => '',
                            'version' => '1.1',
                        ],
                        'responseModel' => [
                            'status' => 200,
                            'headers' => [
                                'Date' => [
                                    'Sun, 21 Jul 2024 16:26:25 GMT',
                                ],
                                'Content-Type' => [
                                    'application/json',
                                ],
                                'Content-Length' => [
                                    '53',
                                ],
                                'Connection' => [
                                    'keep-alive',
                                ],
                                'Server' => [
                                    'gunicorn/19.9.0',
                                ],
                                'Access-Control-Allow-Origin' => [
                                    '*',
                                ],
                                'Access-Control-Allow-Credentials' => [
                                    'true',
                                ],
                            ],
                            'body' => '{' . "\n"
                                . '  "uuid": "e05cdd05-879d-45bc-94f2-ddefa584822f"' . "\n"
                                . '}' . "\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                            'decodedBody' => [
                                'uuid' => 'e05cdd05-879d-45bc-94f2-ddefa584822f',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}