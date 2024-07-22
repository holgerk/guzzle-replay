<?php

namespace Holgerk\GuzzleReplay\Tests\examples;

use GuzzleHttp\Client;

class SimpleApiClient
{
    public function __construct(
        private Client $client
    ) {}

    public function getUuid(): string
    {
        $content = $this->client
            ->get('https://httpbin.org/uuid')
            ->getBody()
            ->getContents();
        return json_decode($content, true)['uuid'];
    }

    public function getStatusCode(int $code): string
    {
        $response = $this->client->get(
            'https://httpbin.org/status/' . $code,
            [
                'http_errors' => false,
                'allow_redirects' => false,
            ]
        );
        return $response->getStatusCode();
    }
}