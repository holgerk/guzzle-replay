<?php

use Holgerk\GuzzleReplay\RequestModel;
use Holgerk\GuzzleReplay\ResponseModel;

/** @param array{method?: string, uri?: string, headers?: array, body?: string, version?: string} $data */
function makeRequest(array $data = []): RequestModel
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
function makeResponse(array $data = []): ResponseModel
{
    return ResponseModel::fromArray([
        'status' => $data['status'] ?? 200,
        'headers' => $data['headers'] ?? [],
        'body' => $data['body'] ?? '',
        'version' => $data['version'] ?? '',
        'reason' => $data['reason'] ?? '',
    ]);
}