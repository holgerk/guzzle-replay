# guzzle-replay middleware

![GitHub Release](https://img.shields.io/github/v/release/holgerk/guzzle-replay)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/holgerk/guzzle-replay/tests.yml)
![Packagist Downloads](https://img.shields.io/packagist/dt/holgerk/guzzle-replay)

Record guzzle requests and have them replayed during next runs. This helps creating tests for http api clients. 


## Install
```
composer require holgerk/guzzle-replay --dev
```

## Usage
```php
use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\Mode;
use Holgerk\GuzzleReplay\GuzzleReplay;

$guzzleClient = new Client();
// create middleware either in recording or in replay mode
//$middleware = GuzzleReplay::create(Mode::Replay);
$middleware = GuzzleReplay::create(Mode::Record);
// inject middleware into guzzle client
$middleware->inject($guzzleClient);
// inject guzzle client into to your api client that you want to test
$apiClient = new GithubApiClient($guzzleClient);
// do your tests with the api client...
```

## Example

<details>

<summary>SimpleApiClient.php</summary>

```php
use GuzzleHttp\Client;

class SimpleApiClient
{
    public function __construct(private Client $client) {}

    public function getUuid(): string
    {
        $content = $this->client
            ->get('https://httpbin.org/uuid')
            ->getBody()
            ->getContents();
        return json_decode($content, true)['uuid'];
    }
}
```

</details>

<details>

<summary>SimpleApiClientTest.php</summary>

```php
use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\GuzzleReplay;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class SimpleApiClientTest extends TestCase
{
    public function testGetUuid(): void
    {
        // GIVEN
        $guzzleClient = new Client();
        $middleware = GuzzleReplay::create(GuzzleReplay::MODE_REPLAY);
        $middleware->inject($guzzleClient);

        // WHEN
        $apiClient = new SimpleApiClient($guzzleClient);
        $firstUuid = $apiClient->getUuid();
        $secondUuid = $apiClient->getUuid();

        // THEN
        assertEquals('bdd37445-2455-44d3-a00d-18d6220ff565', $firstUuid);
        assertEquals('ea175c37-df2a-42aa-b9b8-c3eac8dfb80b', $secondUuid);
    }
}
```

</details>

- This following file is generated on first recording and updated on following recordings. 
- It contains all responses and requests that happen during the recording.
- The name of the file is composed of test class name, test method name and the suffix: "_guzzleRecording.php".
- If you don't like to have the recordings in separate files you can opt-out (see: Recording to method and not to a file)

<details>

<summary>SimpleApiClientTest_testGetUuid_guzzleRecording.php</summary>

```php
<?php

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
                            'Wed, 07 May 2025 09:16:13 GMT',
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
                    'body' => '{'."\n"
                        .'  "uuid": "bdd37445-2455-44d3-a00d-18d6220ff565"'."\n"
                        .'}'."\n",
                    'version' => '1.1',
                    'reason' => 'OK',
                    'decodedBody' => [
                        'uuid' => 'bdd37445-2455-44d3-a00d-18d6220ff565',
                    ],
                ],
            ],
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
                            'Wed, 07 May 2025 09:16:13 GMT',
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
                    'body' => '{'."\n"
                        .'  "uuid": "ea175c37-df2a-42aa-b9b8-c3eac8dfb80b"'."\n"
                        .'}'."\n",
                    'version' => '1.1',
                    'reason' => 'OK',
                    'decodedBody' => [
                        'uuid' => 'ea175c37-df2a-42aa-b9b8-c3eac8dfb80b',
                    ],
                ],
            ],
        ],
    ]
);
```

</details>

## Errors for unexpected requests

<details>

<summary>Output</summary>

```
1) Holgerk\GuzzleReplay\Tests\examples\SimpleApiClientTest::testMultipleRequests
Holgerk\GuzzleReplay\NoReplayFoundAssertionError:
| No replay found for this request:
| ---------------------------------
| - Request
|     method: GET
|     uri: https://httpbin.org/status/201
|     headers: {"User-Agent":["GuzzleHttp\/7"],"Host":["httpbin.org"]}
|     body:
|     version: 1.1
|
| Diff to best matching expected request:
| ---------------------------------------
| --- Expected
| +++ Actual
| @@ @@
|  Request
|      method: GET
| -    uri: https://httpbin.org/status/200
| +    uri: https://httpbin.org/status/201
|      headers: {"User-Agent":["GuzzleHttp\/7"],"Host":["httpbin.org"]}
|      body:
|      version: 1.1
|
| All expected requests (sorted by difference):
| ---------------------------------------------
| - Request
|     method: GET
|     uri: https://httpbin.org/status/200
|     headers: {"User-Agent":["GuzzleHttp\/7"],"Host":["httpbin.org"]}
|     body:
|     version: 1.1
|
| - Request
|     method: GET
|     uri: https://httpbin.org/status/303
|     headers: {"User-Agent":["GuzzleHttp\/7"],"Host":["httpbin.org"]}
|     body:
|     version: 1.1
|
```

</details>

## Usage with Laravel Http Facade
```php
use Illuminate\Support\Facades\Http;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\Mode;
Http::globalMiddleware(GuzzleReplay::create(Mode::Replay));
```

## Recording to method and not to a file

*globally (for all tests)*
```php
use Holgerk\GuzzleReplay\MethodRecorder;
use Holgerk\GuzzleReplay\Options;
Options::$globalRecorderFactory = fn() => new MethodRecorder();
```

*locally (within one test)*
```php
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\MethodRecorder;
use Holgerk\GuzzleReplay\Options;
$middleware = GuzzleReplay::create(
    Mode::Record, 
    Options::create()->setRecorder(new MethodRecorder())
);
```

## Masking sensistive data
```php
$middleware = GuzzleReplay::create(Mode::Replay, Options::create()
    ->setRequestTransformer(static function (RequestModel $requestModel) {
        // mask authorization token, to not leak sensitive data
        $requestModel->replaceString($_ENV['GITHUB_TOKEN'], 'XXX');
        // or you can unset the header 
        //unset($requestModel->headers['Authorization']);
        //$requestModel->removeHeader('content-length');
    })
);
```

## Usage with dataProviders
```php
public static function dataProviderTestGetStatusCode(): array
{
    return [
        'data-set-1' => ['givenStatusCode' => 201],
        'data-set-2' => ['givenStatusCode' => 400],
    ];
}

/**
 * @dataProvider dataProviderTestGetStatusCode
 */
public function testGetStatusCode(int $givenStatusCode): void
{
    // GIVEN
     
    // append status code to testMethodName so we get distinct 
    // recordings foreach data-set.
    $options = Options::create();
    $options->recordName->testMethodName .= $givenStatusCode;
    
    $guzzleClient = new Client();
    $middleware = GuzzleReplay::create(GuzzleReplay::MODE_REPLAY, $options);
    $middleware->inject($guzzleClient);

    // WHEN
    $apiClient = new SimpleApiClient($guzzleClient);
    $responseStatusCode = $apiClient->getStatusCode($givenStatusCode);

    // THEN
    assertEquals($givenStatusCode, $responseStatusCode);
}
```

## Best practices
- Use recordings to test every public method of your api client, buttests of your api client consumer you should use a mocked version of your api client. Otherwise you pollute your tests with redundant recordings, which require you todo many rerecordings, when you change the implementation of one of your api clients methods. 

## FAQ
- Why not just mock the guzzle client?
  Yes, this it is also an option. But if you want to ensure that your api client does not haved changed the exact http invocation, which in fact invalidates your fixtured response, one would need alot of verification boilerplate. Also it is teddious to fixture the exact response.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
