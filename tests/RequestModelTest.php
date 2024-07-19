<?php

namespace Holgerk\GuzzleReplay\Tests;

use PHPUnit\Framework\TestCase;
use function Holgerk\AssertGolden\assertGolden;

class RequestModelTest extends TestCase
{
    public function testReplaceString(): void
    {
        $request = makeRequest([
            'uri' => 'http://X_find_me_X',
            'body' => 'X find_me X',
            'headers' => [
                'some-header' => [
                    'X find_me X'
                ]
            ]
        ]);

        $request->replaceString('find_me', 'found_you');

        assertGolden(
            'Request ' . "\n"
                . '    method: GET' . "\n"
                . '    uri: http://X_found_you_X' . "\n"
                . '    headers: {"some-header":["X found_you X"]}' . "\n"
                . '    body: X found_you X' . "\n"
                . '    version: ',
            $request->__toString()
        );
    }
}
