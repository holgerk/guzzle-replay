<?php

namespace Holgerk\GuzzleReplay\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\Middleware;
use Holgerk\GuzzleReplay\Mode;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{

    #[Test]
    public function record_test(): void
    {
        $stack = HandlerStack::create();
        $middleware = Middleware::create(Mode::Record);
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);

        $response = $client->get('https://httpbin.org/uuid');
        $response = $client->get('https://httpbin.org/status/400', ['http_errors' => false]);
//        echo "response: " . ($response->getBody()->getContents()) . "\n";
//        echo "recording: " . json_encode($middleware->getRecording(), JSON_PRETTY_PRINT) . "\n";
    }

    #[Test]
    public function replay_test(): void
    {
        $stack = HandlerStack::create();
        $middleware = Middleware::create(Mode::Replay);
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);

        $response = $client->get('https://httpbin.org/uuid');
        echo "response: " . ($response->getBody()->getContents()) . "\n";
    }

    public static function record_testGuzzleRecording(): \Holgerk\GuzzleReplay\Recording
    {
        // generated - do not edit
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
                                        "Wed, 10 Apr 2024 07:28:30 GMT"
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
                                "body": "{\n  \"uuid\": \"f40c713d-202c-4d02-92ec-2c4806ad25af\"\n}\n",
                                "version": "1.1",
                                "reason": "OK"
                            }
                        },
                        {
                            "requestModel": {
                                "method": "GET",
                                "uri": "https:\/\/httpbin.org\/status\/400",
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
                                "status": 400,
                                "headers": {
                                    "Date": [
                                        "Wed, 10 Apr 2024 07:28:30 GMT"
                                    ],
                                    "Content-Type": [
                                        "text\/html; charset=utf-8"
                                    ],
                                    "Content-Length": [
                                        "0"
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
                                "body": "",
                                "version": "1.1",
                                "reason": "BAD REQUEST"
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

    public static function replay_testGuzzleRecording(): \Holgerk\GuzzleReplay\Recording
    {
        // generated - do not edit
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
                                        "Wed, 10 Apr 2024 07:37:30 GMT"
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
                                "body": "{\n  \"uuid\": \"f5a3d210-5ddf-47c4-80b2-3fce8cc3e424\"\n}\n",
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
