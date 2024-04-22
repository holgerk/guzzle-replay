<?php

namespace Holgerk\GuzzleReplay\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\Middleware;
use Holgerk\GuzzleReplay\Mode;
use Holgerk\GuzzleReplay\Options;
use Holgerk\GuzzleReplay\RecordName;
use Holgerk\GuzzleReplay\RequestModel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;
use function Holgerk\AssertGolden\assertGolden;

#[CoversClass(Middleware::class)]
class MiddlewareTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // start test server
        $testServer = __DIR__ . '/test-server.php';
        $process = new Process(['php', '-S', 'localhost:8000', $testServer]);
        $process->start();
        $client = new Client();
        $tries = 10;
        while ($tries-- >= 0) {
            try {
                $client->get('http://localhost:8000');
                break;
            } catch (Throwable) {
                usleep(1000 * 50);
            }
            if ($tries === 0) {
                throw new RuntimeException("Failed to reach test server");
            }
        }
    }

    public static function record_test_dataProvider(): array
    {
        return [
            'case NewRecording'    => ['className' => 'NewRecording'],
            'case UpdateRecording' => ['className' => 'UpdateRecording'],
        ];
    }

    /**
     * @test
     * @dataProvider record_test_dataProvider
     */
    public function record_test(string $className): void
    {
        copy(__DIR__ . "/cases/$className.before.php", __DIR__ . "/cases/$className.test.php");
        include __DIR__ . "/cases/$className.test.php";
        $fqnClassName = '\\Holgerk\\GuzzleReplay\\Tests\\cases\\' . $className;
        $case = new $fqnClassName();
        $middleware = $case->executeTest();
        $middleware->writeRecording();
        self::assertFileEquals(
            __DIR__ . "/cases/$className.expected.php",
            __DIR__ . "/cases/$className.test.php",
        );
    }

    public function testReplay(): void
    {
        $stack = HandlerStack::create();
        $middleware = Middleware::create(Mode::Replay);
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);

        $response = $client->get('https://httpbin.org/uuid');
        $data = json_decode($response->getBody()->getContents());
        // normally https://httpbin.org/uuid would answer with a new uuid, but we use
        // our recording and this will have a fixed value
        self::assertEquals('254c7adc-456a-4f6d-8255-dc752396b82b', $data->uuid);
    }

    public function testRequestTransformer(): void
    {
        $recorder = new TestRecorder();

        $stack = HandlerStack::create();
        $middleware = Middleware::create(Mode::Record, Options::create()
            ->setRequestTransformer(function (RequestModel $requestModel) {
                $requestModel->uri = str_replace('localhost', 'host', $requestModel->uri);
            })
            ->setRecorder($recorder)
        );
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);

        $client->get('http://localhost:8000/?queryParam=42');

        $records = $recorder->getRecording()->getRecords();
        self::assertCount(1, $records);
        // localhost is normalized to host
        assertGolden('http://host:8000/?queryParam=42', $records[0]->requestModel->uri);
    }

    public function testCustomRecordName(): void
    {
        $stack = HandlerStack::create();
        $middleware = Middleware::create(Mode::Replay, Options::create()
            ->setRecordName(RecordName::make(__CLASS__, __FUNCTION__))
        );
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);

        $response = $client->get('https://httpbin.org/uuid');
        $data = json_decode($response->getBody()->getContents());
        // normally https://httpbin.org/uuid would answer with a new uuid, but we use
        // our recording and this will have a fixed value
        self::assertEquals('e418681e-9d38-4d69-b661-584a19d6861d', $data->uuid);
    }

    public function testInject(): void
    {
        $client = new Client();
        Middleware::inject($client, Mode::Replay);
        $response = $client->get('https://httpbin.org/uuid');
        $data = json_decode($response->getBody()->getContents());
        self::assertEquals('b32f97f9-db0d-4614-ba1e-a777c02864c3', $data->uuid);
    }

    public static function guzzleRecording_testReplay(): \Holgerk\GuzzleReplay\Recording
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
                                    'Wed, 17 Apr 2024 06:20:24 GMT',
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
                            'body' => '{'."\n"
                                .'  "uuid": "254c7adc-456a-4f6d-8255-dc752396b82b"'."\n"
                                .'}'."\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                        ],
                    ],
                ],
            ]
        );
    }

    public static function guzzleRecording_testCustomRecordName(): \Holgerk\GuzzleReplay\Recording
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
                                    'Mon, 22 Apr 2024 20:54:22 GMT',
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
                            'body' => '{'."\n"
                                .'  "uuid": "e418681e-9d38-4d69-b661-584a19d6861d"'."\n"
                                .'}'."\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                        ],
                    ],
                ],
            ]
        );
    }

    public static function guzzleRecording_testInject(): \Holgerk\GuzzleReplay\Recording
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
                                    'Mon, 22 Apr 2024 21:36:39 GMT',
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
                            'body' => '{'."\n"
                                .'  "uuid": "b32f97f9-db0d-4614-ba1e-a777c02864c3"'."\n"
                                .'}'."\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                        ],
                    ],
                ],
            ]
        );
    }

}
