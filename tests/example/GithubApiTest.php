<?php

namespace Holgerk\GuzzleReplay\Tests\example;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\Options;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\RequestModel;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\TestCase;
use function Holgerk\AssertGolden\assertGolden;

class GithubApiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // load env with GITHUB_TOKEN
        if (file_exists(dirname(__DIR__, 2) . '/.env')) {
            Dotenv::createImmutable(dirname(__DIR__, 2))->load();
        }
        if (! isset($_ENV['GITHUB_TOKEN'])) {
            $_ENV['GITHUB_TOKEN'] = 'test-token';
        }
    }

    public function testLaravelFacade(): void
    {
        // set the underlying instance behind the facade,
        // this is normally done by laravels dependency container
        Http::swap(new \Illuminate\Http\Client\Factory());

        Http::globalMiddleware(GuzzleReplay::create(GuzzleReplay::MODE_REPLAY));
        assertGolden(
            ['uuid' => 'd7e0d101-16ae-4250-9c2c-97d10dc9e0fe'],
            Http::get('https://httpbin.org/uuid')->json()
        );
    }

    public function testSimple(): void
    {
        $client = new Client();
        $middleware = GuzzleReplay::create(
            GuzzleReplay::MODE_REPLAY,
            Options::create()
                ->setRequestTransformer(static function (RequestModel $requestModel) {
                    // mask authorization token, to not leak sensitive data
                    $requestModel->replaceString($_ENV['GITHUB_TOKEN'], 'XXX');
                    // or you can unset the header 
                    //unset($requestModel->headers['Authorization']);
                })
        );

        $middleware->inject($client);
        $api = new GithubApi($client);
        assertGolden(['v0.1.0'], $api->getTagNames());
    }

    public static function guzzleRecording_testSimple(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://api.github.com/repos/holgerk/guzzle-replay/tags',
                            'headers' => [
                                'User-Agent' => [
                                    'GuzzleHttp/7',
                                ],
                                'Host' => [
                                    'api.github.com',
                                ],
                                'Accept' => [
                                    'application/vnd.github+json',
                                ],
                                'Authorization' => [
                                    'Bearer XXX',
                                ],
                                'X-GitHub-Api-Version' => [
                                    '2022-11-28',
                                ],
                            ],
                            'body' => '',
                            'version' => '1.1',
                        ],
                        'responseModel' => [
                            'status' => 200,
                            'headers' => [
                                'Date' => [
                                    'Fri, 19 Jul 2024 08:45:59 GMT',
                                ],
                                'Content-Type' => [
                                    'application/json; charset=utf-8',
                                ],
                                'Content-Length' => [
                                    '420',
                                ],
                                'Cache-Control' => [
                                    'private, max-age=60, s-maxage=60',
                                ],
                                'Vary' => [
                                    'Accept, Authorization, Cookie, X-GitHub-OTP,Accept-Encoding, Accept, X-Requested-With',
                                ],
                                'ETag' => [
                                    '"1c694af3ff1864e2f69790a3c431d7bfa85c2a3e8a80b5c12ec28bb0e3d0b11d"',
                                ],
                                'Last-Modified' => [
                                    'Thu, 18 Jul 2024 14:12:29 GMT',
                                ],
                                'github-authentication-token-expiration' => [
                                    '2024-10-17 10:34:01 +0200',
                                ],
                                'X-GitHub-Media-Type' => [
                                    'github.v3; format=json',
                                ],
                                'x-accepted-github-permissions' => [
                                    'metadata=read',
                                ],
                                'x-github-api-version-selected' => [
                                    '2022-11-28',
                                ],
                                'X-RateLimit-Limit' => [
                                    '5000',
                                ],
                                'X-RateLimit-Remaining' => [
                                    '4985',
                                ],
                                'X-RateLimit-Reset' => [
                                    '1721380813',
                                ],
                                'X-RateLimit-Used' => [
                                    '15',
                                ],
                                'X-RateLimit-Resource' => [
                                    'core',
                                ],
                                'Access-Control-Expose-Headers' => [
                                    'ETag, Link, Location, Retry-After, X-GitHub-OTP, X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Used, X-RateLimit-Resource, X-RateLimit-Reset, X-OAuth-Scopes, X-Accepted-OAuth-Scopes, X-Poll-Interval, X-GitHub-Media-Type, X-GitHub-SSO, X-GitHub-Request-Id, Deprecation, Sunset',
                                ],
                                'Access-Control-Allow-Origin' => [
                                    '*',
                                ],
                                'Strict-Transport-Security' => [
                                    'max-age=31536000; includeSubdomains; preload',
                                ],
                                'X-Frame-Options' => [
                                    'deny',
                                ],
                                'X-Content-Type-Options' => [
                                    'nosniff',
                                ],
                                'X-XSS-Protection' => [
                                    '0',
                                ],
                                'Referrer-Policy' => [
                                    'origin-when-cross-origin, strict-origin-when-cross-origin',
                                ],
                                'Content-Security-Policy' => [
                                    'default-src \'none\'',
                                ],
                                'Server' => [
                                    'github.com',
                                ],
                                'X-GitHub-Request-Id' => [
                                    '08FA:26B667:13331BA:1381542:669A27C6',
                                ],
                            ],
                            'body' => '[{"name":"v0.1.0","zipball_url":"https://api.github.com/repos/holgerk/guzzle-replay/zipball/refs/tags/v0.1.0","tarball_url":"https://api.github.com/repos/holgerk/guzzle-replay/tarball/refs/tags/v0.1.0","commit":{"sha":"e88eb3aa4f57afad0f792d50217737c80617a993","url":"https://api.github.com/repos/holgerk/guzzle-replay/commits/e88eb3aa4f57afad0f792d50217737c80617a993"},"node_id":"REF_kwDOLsRAD7ByZWZzL3RhZ3MvdjAuMS4w"}]',
                            'version' => '1.1',
                            'reason' => 'OK',
                            'decodedBody' => [
                                [
                                    'name' => 'v0.1.0',
                                    'zipball_url' => 'https://api.github.com/repos/holgerk/guzzle-replay/zipball/refs/tags/v0.1.0',
                                    'tarball_url' => 'https://api.github.com/repos/holgerk/guzzle-replay/tarball/refs/tags/v0.1.0',
                                    'commit' => [
                                        'sha' => 'e88eb3aa4f57afad0f792d50217737c80617a993',
                                        'url' => 'https://api.github.com/repos/holgerk/guzzle-replay/commits/e88eb3aa4f57afad0f792d50217737c80617a993',
                                    ],
                                    'node_id' => 'REF_kwDOLsRAD7ByZWZzL3RhZ3MvdjAuMS4w',
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    public static function guzzleRecording_testLaravelFacade(): \Holgerk\GuzzleReplay\Recording
    {
        // GENERATED - DO NOT EDIT
        return \Holgerk\GuzzleReplay\Recording::fromArray(
            [
                'records' => [
                    [
                        'requestModel' => [
                            'method' => 'GET',
                            'uri' => 'https://httpbin.org/uuid',
                            'headers' => [
                                'User-Agent' => [
                                    'GuzzleHttp/7',
                                ],
                                'Host' => [
                                    'httpbin.org',
                                ],
                            ],
                            'body' => '',
                            'version' => '1.1',
                        ],
                        'responseModel' => [
                            'status' => 200,
                            'headers' => [
                                'Date' => [
                                    'Sat, 13 Jul 2024 11:50:27 GMT',
                                ],
                                'Content-Type' => [
                                    'application/json',
                                ],
                                'Content-Length' => [
                                    '53',
                                ],
                                'Connection' => [
                                    'keep-alive',
                                ],
                                'Server' => [
                                    'gunicorn/19.9.0',
                                ],
                                'Access-Control-Allow-Origin' => [
                                    '*',
                                ],
                                'Access-Control-Allow-Credentials' => [
                                    'true',
                                ],
                            ],
                            'body' => '{' . "\n"
                                . '  "uuid": "d7e0d101-16ae-4250-9c2c-97d10dc9e0fe"' . "\n"
                                . '}' . "\n",
                            'version' => '1.1',
                            'reason' => 'OK',
                            'decodedBody' => [
                                'uuid' => 'd7e0d101-16ae-4250-9c2c-97d10dc9e0fe',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
}
