<?php

namespace Holgerk\GuzzleReplay\Tests\cases;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\Middleware;
use Holgerk\GuzzleReplay\Mode;

class NewRecording {

    public function executeTest(): Middleware
    {
        $stack = HandlerStack::create();
        $middleware = Middleware::create(Mode::Record);
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);
        $client->get('http://localhost:8000/?queryParam=42');
        return $middleware;
    }

    public static function executeTestGuzzleRecording(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromJson(json_decode(
            <<<'_JSON_'
            {
                "records": [
                    {
                        "requestModel": {
                            "method": "GET",
                            "uri": "http:\/\/localhost:8000\/?queryParam=42",
                            "headers": {
                                "User-Agent": [
                                    "GuzzleHttp\/7"
                                ],
                                "Host": [
                                    "localhost:8000"
                                ]
                            },
                            "body": "",
                            "version": "1.1"
                        },
                        "responseModel": {
                            "status": 200,
                            "headers": {
                                "Host": [
                                    "localhost:8000"
                                ],
                                "Connection": [
                                    "close"
                                ],
                                "Content-Type": [
                                    "application\/json"
                                ],
                                "Date": [
                                    "Sat, 13 Apr 2024 14:22:40 GMT"
                                ],
                                "X-Powered-By": [
                                    "PHP"
                                ]
                            },
                            "body": "{\"queryParam\":\"42\"}",
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