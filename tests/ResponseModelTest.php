<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\ResponseModel;
use PHPUnit\Framework\TestCase;

class ResponseModelTest extends TestCase
{
    public function test_decoded_body(): void
    {
        $response = makeResponse([
            'headers' => [
                'CoNtent-TyPe' => [
                    'application/jSon'
                ]
            ],
            'body' => '{"property": 42}',
        ]);

        self::assertEquals(42, $response->toArray()['decodedBody']['property']);
    }
}
