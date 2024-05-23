<?php

namespace Holgerk\GuzzleReplay\Tests\ReplayMiddlewareTest_record_test;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Holgerk\GuzzleReplay\Options;
use Holgerk\GuzzleReplay\ReplayMiddleware;
use Holgerk\GuzzleReplay\Mode;
use Holgerk\GuzzleReplay\ResponseModel;

class UpdateRecording {

    public function executeTest(): ReplayMiddleware
    {
        $client = new Client();
        $middleware = ReplayMiddleware::inject($client, Mode::Record, Options::create()
            ->setResponseTransformer(
                static function (ResponseModel $responseModel) {
                    // use a fixed value to make assertions easier
                    $responseModel->headers['Date'] = ['Thu, 23 May 2024 06:25:25 GMT'];
                }
            )
        );
        $client->get('http://localhost:8000/?queryParam=42');
        return $middleware;
    }

    public static function guzzleRecording_executeTest(): \Holgerk\GuzzleReplay\Recording
    {

    }
}