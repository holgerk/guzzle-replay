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

    }
}