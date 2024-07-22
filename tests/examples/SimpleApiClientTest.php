<?php

namespace Holgerk\GuzzleReplay\Tests\examples;

use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\Options;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class SimpleApiClientTest extends TestCase
{
    public function testGetUuid(): void
    {
        // GIVEN
        $guzzleClient = new Client();
        $middleware = GuzzleReplay::create(GuzzleReplay::MODE_REPLAY);
        $middleware->inject($guzzleClient);

        // WHEN
        $apiClient = new SimpleApiClient($guzzleClient);
        $firstUuid = $apiClient->getUuid();
        $secondUuid = $apiClient->getUuid();

        // THEN
        assertEquals('f7b85c93-f24c-4a5c-895f-b2e89bd5d4bc', $firstUuid);
        assertEquals('7761590c-24c9-4d82-aad4-7b890f9beb97', $secondUuid);
    }

    public static function dataProviderTestGetStatusCode(): array
    {
        return [
            'data-set-1' => ['givenStatusCode' => 201],
            'data-set-2' => ['givenStatusCode' => 400],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetStatusCode
     */
    public function testGetStatusCode(int $givenStatusCode): void
    {
        // GIVEN

        // append status code to testMethodName so we get distinct 
        // recordings foreach data-set.
        $options = Options::create();
        $options->recordName->testMethodName .= $givenStatusCode;

        $guzzleClient = new Client();
        $middleware = GuzzleReplay::create(GuzzleReplay::MODE_REPLAY, $options);
        $middleware->inject($guzzleClient);

        // WHEN
        $apiClient = new SimpleApiClient($guzzleClient);
        $responseStatusCode = $apiClient->getStatusCode($givenStatusCode);

        // THEN
        assertEquals($givenStatusCode, $responseStatusCode);
    }
    
    public function testMultipleRequests(): void
    {
        // GIVEN
        $guzzleClient = new Client();
        $middleware = GuzzleReplay::create(GuzzleReplay::MODE_REPLAY);
        $middleware->inject($guzzleClient);

        // WHEN
        $apiClient = new SimpleApiClient($guzzleClient);
        $firstStatusCode = $apiClient->getStatusCode(200);
        $secondStatusCode = $apiClient->getStatusCode(303);

        // THEN
        assertEquals(200, $firstStatusCode);
        assertEquals(303, $secondStatusCode);
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
                                    'Sun, 21 Jul 2024 19:35:16 GMT',
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
                                . '  "uuid": "f7b85c93-f24c-4a5c-895f-b2e89bd5d4bc"' . "\n"
                                . '}' . "\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                            'decodedBody' => [
                                'uuid' => 'f7b85c93-f24c-4a5c-895f-b2e89bd5d4bc',
                            ],
                        ],
                    ],
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
                                    'Sun, 21 Jul 2024 19:35:16 GMT',
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
                                . '  "uuid": "7761590c-24c9-4d82-aad4-7b890f9beb97"' . "\n"
                                . '}' . "\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                            'decodedBody' => [
                                'uuid' => '7761590c-24c9-4d82-aad4-7b890f9beb97',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public static function guzzleRecording_testGetStatusCode201(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://httpbin.org/status/201',
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
                            'status' => 201,
                            'headers' => [
                                'Date' => [
                                    'Sun, 21 Jul 2024 19:59:58 GMT',
                                ],
                                'Content-Type' => [
                                    'text/html; charset=utf-8',
                                ],
                                'Content-Length' => [
                                    '0',
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
                            'body' => '',
                            'version' => '1.1',
                            'reason' => 'CREATED',
                        ],
                    ],
                ],
            ]
        );
    }

    public static function guzzleRecording_testGetStatusCode400(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://httpbin.org/status/400',
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
                            'status' => 400,
                            'headers' => [
                                'Date' => [
                                    'Sun, 21 Jul 2024 19:59:59 GMT',
                                ],
                                'Content-Type' => [
                                    'text/html; charset=utf-8',
                                ],
                                'Content-Length' => [
                                    '0',
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
                            'body' => '',
                            'version' => '1.1',
                            'reason' => 'BAD REQUEST',
                        ],
                    ],
                ],
            ]
        );
    }
    
    public static function guzzleRecording_testMultipleRequests(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://httpbin.org/status/200',
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
                                    'Sun, 21 Jul 2024 20:21:11 GMT',
                                ],
                                'Content-Type' => [
                                    'text/html; charset=utf-8',
                                ],
                                'Content-Length' => [
                                    '0',
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
                            'body' => '',
                            'version' => '1.1',
                            'reason' => 'OK',
                        ],
                    ],
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://httpbin.org/status/303',
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
                            'status' => 303,
                            'headers' => [
                                'Date' => [
                                    'Sun, 21 Jul 2024 20:21:11 GMT',
                                ],
                                'Content-Length' => [
                                    '0',
                                ],
                                'Connection' => [
                                    'keep-alive',
                                ],
                                'Server' => [
                                    'gunicorn/19.9.0',
                                ],
                                'location' => [
                                    '/redirect/1',
                                ],
                                'Access-Control-Allow-Origin' => [
                                    '*',
                                ],
                                'Access-Control-Allow-Credentials' => [
                                    'true',
                                ],
                            ],
                            'body' => '',
                            'version' => '1.1',
                            'reason' => 'SEE OTHER',
                        ],
                    ],
                ],
            ]
        );
    }
}