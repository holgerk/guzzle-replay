<?php

namespace Holgerk\GuzzleReplay\Tests\examples;

use GuzzleHttp\Client;

class SimpleApi
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
}