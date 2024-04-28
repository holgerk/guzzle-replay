<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\Record;
use Holgerk\GuzzleReplay\Recording;
use PHPUnit\Framework\TestCase;
use Throwable;
use function Holgerk\AssertGolden\assertGolden;

class RecordingTest extends TestCase
{
    public function testFindResponse(): void
    {
        $recording = new Recording();
        $recording->addRecord(new Record(
            makeRequest(),
            makeResponse(['status' => 404])
        ));
        $response = $recording->getReplayResponse(makeRequest());
        self::assertEquals(404, $response->getStatusCode());
    }

    public function testReplayAlreadyUsedException(): void
    {
        $recording = new Recording();
        $recording->addRecord(new Record(makeRequest(), makeResponse()));

        $recording->getReplayResponse(makeRequest());
        $message = null;
        try {
            $recording->getReplayResponse(makeRequest());
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }
        assertGolden(
            "\n"
                .'| Replay for this request was already used:'."\n"
                .'| -----------------------------------------'."\n"
                .'| - Request '."\n"
                .'|     method: GET'."\n"
                .'|     uri: '."\n"
                .'|     headers: []'."\n"
                .'|     body: '."\n"
                .'|     version: ',
            $message
        );
    }

    public function testNoReplayAlreadyUsedException(): void
    {
        self::expectNotToPerformAssertions();

        $recording = new Recording();

        // add same request twice
        $recording->addRecord(new Record(makeRequest(), makeResponse()));
        $recording->addRecord(new Record(makeRequest(), makeResponse()));

        $recording->getReplayResponse(makeRequest());
        $recording->getReplayResponse(makeRequest());
    }

    public function testNoReplayFoundException(): void
    {
        $recording = new Recording();
        $recording->addRecord(new Record(
            makeRequest(['method' => 'PATCH', 'uri' => '/request-different']),
            makeResponse(['status' => 404])
        ));
        $recording->addRecord(new Record(
            makeRequest(['method' => 'GET', 'uri' => '/request-different']),
            makeResponse(['status' => 404])
        ));
        $message = '';
        try {
            $recording->getReplayResponse(
                makeRequest(['method' => 'POST', 'uri' => '/request-something'])
            );
        } catch (Throwable $e) {
            $message = $e->getMessage();
        }

        assertGolden(
            "\n"
            . '| No replay found for this request:' . "\n"
            . '| ---------------------------------' . "\n"
            . '| - Request ' . "\n"
            . '|     method: POST' . "\n"
            . '|     uri: /request-something' . "\n"
            . '|     headers: []' . "\n"
            . '|     body: ' . "\n"
            . '|     version: ' . "\n"
            . '| ' . "\n"
            . '| Diff to best matching expected request:' . "\n"
            . '| ---------------------------------------' . "\n"
            . '| --- Expected' . "\n"
            . '| +++ Actual' . "\n"
            . '| @@ @@' . "\n"
            . '|  Request ' . "\n"
            . '| -    method: GET' . "\n"
            . '| -    uri: /request-different' . "\n"
            . '| +    method: POST' . "\n"
            . '| +    uri: /request-something' . "\n"
            . '|      headers: []' . "\n"
            . '|      body: ' . "\n"
            . '|      version:' . "\n"
            . '| ' . "\n"
            . '| All expected requests (sorted by difference):' . "\n"
            . '| ---------------------------------------------' . "\n"
            . '| - Request ' . "\n"
            . '|     method: GET' . "\n"
            . '|     uri: /request-different' . "\n"
            . '|     headers: []' . "\n"
            . '|     body: ' . "\n"
            . '|     version: ' . "\n"
            . '| ' . "\n"
            . '| - Request ' . "\n"
            . '|     method: PATCH' . "\n"
            . '|     uri: /request-different' . "\n"
            . '|     headers: []' . "\n"
            . '|     body: ' . "\n"
            . '|     version: ' . "\n"
            . '| ',
            $message
        );
    }



}
