<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\Record;
use Holgerk\GuzzleReplay\Recording;
use Holgerk\GuzzleReplay\RequestModel;
use Holgerk\GuzzleReplay\ResponseModel;
use PHPUnit\Framework\TestCase;
use Throwable;
use function Holgerk\AssertGolden\assertGolden;

class RecordingTest extends TestCase
{
    public function testFindResponse(): void
    {
        $recording = new Recording();
        $recording->addRecord(new Record(
            $this->makeRequest(),
            $this->makeResponse(['status' => 404])
        ));
        $response = $recording->findResponse($this->makeRequest());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNoReplayFoundException(): void
    {
        $recording = new Recording();
        $recording->addRecord(new Record(
            $this->makeRequest(['method' => 'PATCH', 'uri' => '/request-different']),
            $this->makeResponse(['status' => 404])
        ));
        $recording->addRecord(new Record(
            $this->makeRequest(['method' => 'GET', 'uri' => '/request-different']),
            $this->makeResponse(['status' => 404])
        ));
        $message = '';
        try {
            $recording->findResponse(
                $this->makeRequest(['method' => 'POST', 'uri' => '/request-something'])
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


    // helper methods
    // ============================================================================================

    /** @param array{method?: string, uri?: string, headers?: array, body?: string, version?: string} $data */
    private function makeRequest(array $data = []): RequestModel
    {
        return RequestModel::fromArray([
            'method' => $data['method'] ?? 'GET',
            'uri' => $data['uri'] ?? '',
            'headers' => $data['headers'] ?? [],
            'body' => $data['body'] ?? '',
            'version' => $data['version'] ?? '',
        ]);
    }

    /** @param array{status?: int, headers?: array, body?: string, version?: string, reason?: string} $data */
    private function makeResponse(array $data = []): ResponseModel
    {
        return ResponseModel::fromArray([
            'status' => $data['status'] ?? 200,
            'headers' => $data['headers'] ?? [],
            'body' => $data['body'] ?? '',
            'version' => $data['version'] ?? '',
            'reason' => $data['reason'] ?? '',
        ]);
    }
}
