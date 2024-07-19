<?php

namespace Holgerk\GuzzleReplay\Tests;

use PHPUnit\Framework\TestCase;
use function Holgerk\AssertGolden\assertGolden;

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

    public function testReplaceString(): void
    {
        $response = makeResponse([
            'body' => 'X find_me X',
            'headers' => [
                'some-header' => [
                    'X find_me X'
                ]
            ]
        ]);

        $response->replaceString('find_me', 'found_you');

        assertGolden(
            'Request ' . "\n"
                . '    status: 200' . "\n"
                . '    headers: {"some-header":["X found_you X"]}' . "\n"
                . '    body: X found_you X' . "\n"
                . '    version: ' . "\n"
                . '    reason: ',
            $response->__toString()
        );
    }
}
