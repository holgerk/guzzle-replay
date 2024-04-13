<?php

namespace Holgerk\GuzzleReplay\Tests\cases;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\Middleware;
use Holgerk\GuzzleReplay\Mode;

class UpdateRecording {

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

    }
}