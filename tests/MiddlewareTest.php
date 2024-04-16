<?php

namespace Holgerk\GuzzleReplay\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\Middleware;
use Holgerk\GuzzleReplay\Mode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

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
        self::assertEquals('c12f2b32-f51c-4241-83a8-c7d92115a4a8', $data->uuid);
    }

    public static function guzzleRecording_testReplay(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromJson(json_decode(
            <<<'_JSON_'
            {
                "records": [
                    {
                        "requestModel": {
                            "method": "GET",
                            "uri": "https:\/\/httpbin.org\/uuid",
                            "headers": {
                                "User-Agent": [
                                    "GuzzleHttp\/7"
                                ],
                                "Host": [
                                    "httpbin.org"
                                ]
                            },
                            "body": "",
                            "version": "1.1"
                        },
                        "responseModel": {
                            "status": 200,
                            "headers": {
                                "Date": [
                                    "Sat, 13 Apr 2024 20:09:52 GMT"
                                ],
                                "Content-Type": [
                                    "application\/json"
                                ],
                                "Content-Length": [
                                    "53"
                                ],
                                "Connection": [
                                    "keep-alive"
                                ],
                                "Server": [
                                    "gunicorn\/19.9.0"
                                ],
                                "Access-Control-Allow-Origin": [
                                    "*"
                                ],
                                "Access-Control-Allow-Credentials": [
                                    "true"
                                ]
                            },
                            "body": "{\n  \"uuid\": \"c12f2b32-f51c-4241-83a8-c7d92115a4a8\"\n}\n",
                            "version": "1.1",
                            "reason": "OK"
                        }
                    }
                ]
            }
            _JSON_,
            true,
            512,
            JSON_THROW_ON_ERROR
        ));
    }
}
