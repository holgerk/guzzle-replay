<?php

namespace Holgerk\GuzzleReplay\Tests\cases;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\ReplayMiddleware;
use Holgerk\GuzzleReplay\Mode;

class UpdateRecording {

    public function executeTest(): ReplayMiddleware
    {
        $stack = HandlerStack::create();
        $middleware = ReplayMiddleware::create(Mode::Record);
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);
        $client->get('http://localhost:8000/?queryParam=42');
        return $middleware;
    }

    public static function guzzleRecording_executeTest(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'http://localhost:8000/?queryParam=42',
                            'headers' => [
                                'User-Agent' => [
                                    'GuzzleHttp/7',
                                ],
                                'Host' => [
                                    'localhost:8000',
                                ],
                            ],
                            'body' => '',
                            'version' => '1.1',
                        ],
                        'responseModel' => [
                            'status' => 200,
                            'headers' => [
                                'Host' => [
                                    'localhost:8000',
                                ],
                                'Connection' => [
                                    'close',
                                ],
                                'Content-Type' => [
                                    'application/json',
                                ],
                                'Date' => [
                                    'Sat, 13 Apr 2024 14:22:40 GMT',
                                ],
                                'X-Powered-By' => [
                                    'PHP',
                                ],
                            ],
                            'body' => '{'."\n"
                                .'    "queryParam": "42"'."\n"
                                .'}',
                            'version' => '1.1',
                            'reason' => 'OK',
                        ],
                    ],
                ],
            ]
        );
    }
}