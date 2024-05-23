<?php

namespace Holgerk\GuzzleReplay\Tests\example;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Holgerk\GuzzleReplay\Mode;
use Holgerk\GuzzleReplay\Options;
use Holgerk\GuzzleReplay\RecordName;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\RequestModel;
use PHPUnit\Framework\TestCase;

class GithubApiTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->load();
    }

    public function testSimple(): void
    {
        $client = new Client();
        GuzzleReplay::inject($client, Mode::Replay, Options::create()
            ->setRequestTransformer(static function (RequestModel $requestModel) {
                // remove Authorization header, to not leak sensitive data
                unset($requestModel->headers['Authorization']);
            })
        );
        $api = new GithubApi($client);
        self::assertEquals(27, $api->getTotalCommitCount());
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
                            'uri' => 'https://api.github.com/repos/holgerk/guzzle-replay/stats/commit_activity',
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
                                'Server' => [
                                    'GitHub.com',
                                ],
                                'Date' => [
                                    'Wed, 22 May 2024 13:10:31 GMT',
                                ],
                                'Content-Type' => [
                                    'application/json; charset=utf-8',
                                ],
                                'Content-Length' => [
                                    '2759',
                                ],
                                'Cache-Control' => [
                                    'private, max-age=60, s-maxage=60',
                                ],
                                'Vary' => [
                                    'Accept, Authorization, Cookie, X-GitHub-OTP',
                                    'Accept-Encoding, Accept, X-Requested-With',
                                ],
                                'ETag' => [
                                    '"586f706aa7216920db8774e605d43fa1dfc1c06dc4f6d4e8e42e4e94c44a384d"',
                                ],
                                'X-OAuth-Scopes' => [
                                    'repo',
                                ],
                                'X-Accepted-OAuth-Scopes' => [
                                    '',
                                ],
                                'github-authentication-token-expiration' => [
                                    '2024-08-20 05:29:25 UTC',
                                ],
                                'X-GitHub-Media-Type' => [
                                    'github.v3; format=json',
                                ],
                                'x-github-api-version-selected' => [
                                    '2022-11-28',
                                ],
                                'X-RateLimit-Limit' => [
                                    '5000',
                                ],
                                'X-RateLimit-Remaining' => [
                                    '4994',
                                ],
                                'X-RateLimit-Reset' => [
                                    '1716386922',
                                ],
                                'X-RateLimit-Used' => [
                                    '6',
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
                                'X-GitHub-Request-Id' => [
                                    '0D30:314FA9:F219B3A:F3751BE:664DEEC6',
                                ],
                            ],
                            'body' => '[{"days":[0,0,0,0,0,0,0],"total":0,"week":1685232000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1685836800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1686441600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1687046400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1687651200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1688256000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1688860800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1689465600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1690070400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1690675200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1691280000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1691884800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1692489600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1693094400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1693699200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1694304000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1694908800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1695513600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1696118400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1696723200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1697328000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1697932800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1698537600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1699142400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1699750800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1700355600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1700960400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1701565200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1702170000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1702774800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1703379600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1703984400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1704589200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1705194000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1705798800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1706403600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1707008400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1707613200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1708218000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1708822800},{"days":[0,0,0,0,0,0,0],"total":0,"week":1709427600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1710032400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1710633600},{"days":[0,0,0,0,0,0,0],"total":0,"week":1711238400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1711843200},{"days":[0,0,0,1,0,0,1],"total":2,"week":1712448000},{"days":[11,2,2,3,0,0,0],"total":18,"week":1713052800},{"days":[0,3,0,0,0,0,0],"total":3,"week":1713657600},{"days":[4,0,0,0,0,0,0],"total":4,"week":1714262400},{"days":[0,0,0,0,0,0,0],"total":0,"week":1714867200},{"days":[0,0,0,0,0,0,0],"total":0,"week":1715472000},{"days":[0,0,0,0,0,0,0],"total":0,"week":1716076800}]',
                            'version' => '1.1',
                            'reason' => 'OK',
                        ],
                    ],
                ],
            ]
        );
    }
}