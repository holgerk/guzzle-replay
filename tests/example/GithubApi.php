<?php

namespace Holgerk\GuzzleReplay\Tests\example;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class GithubApi
{
    public function __construct(
        private Client $client
    ) {}

    public function getTagNames(): array
    {
        $r = $this->client->get(
            'https://api.github.com/repos/holgerk/guzzle-replay/tags',
            [
                RequestOptions::HEADERS => [
                    'Accept' => 'application/vnd.github+json',
                    'Authorization' => 'Bearer ' . $_ENV['GITHUB_TOKEN'],
                    'X-GitHub-Api-Version' => '2022-11-28',
                ]
            ]
        );
        return array_column(json_decode($r->getBody()->getContents(), true), 'name');
    }
}