<?php

namespace Holgerk\GuzzleReplay\Tests\ReplayMiddlewareTest_record_test;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\ReplayMiddleware;
use Holgerk\GuzzleReplay\Mode;

class NewRecording {

    public function executeTest(): ReplayMiddleware
    {
        $stack = HandlerStack::create();
        $middleware = ReplayMiddleware::create(Mode::Record);
        $stack->push($middleware);
        $client = new Client(['handler' => $stack]);
        $client->get('http://localhost:8000/?queryParam=42');
        return $middleware;
    }

}