<?php

namespace Holgerk\GuzzleReplay\Tests\GuzzleReplayTest_record_test;

use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\MethodRecorder;
use Holgerk\GuzzleReplay\Options;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\Mode;
use Holgerk\GuzzleReplay\ResponseModel;

class NewRecording {
    public function executeTest(): GuzzleReplay
    {
        $client = new Client();
        $middleware = GuzzleReplay::create(
            Mode::Record,
            Options::create()
                ->setResponseTransformer(
                    static function (ResponseModel $responseModel) {
                        // use a fixed value to make assertions easier
                        $responseModel->headers['Date'] = ['Thu, 23 May 2024 06:25:25 GMT'];
                    }
                )
                ->setRecorder(new MethodRecorder())
        );
        $middleware->inject($client);
        $client->get('http://localhost:8000/?queryParam=42');
        return $middleware;
    }
}