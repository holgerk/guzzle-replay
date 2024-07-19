# guzzle-replay middleware

![GitHub Release](https://img.shields.io/github/v/release/holgerk/guzzle-replay)
![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/holgerk/guzzle-replay/tests.yml)
![Packagist Downloads](https://img.shields.io/packagist/dt/holgerk/guzzle-replay)

Record guzzle requests and have them replayed during next runs.


### Usage
```php
use GuzzleHttp\Client;
use Holgerk\GuzzleReplay\Mode;
use Holgerk\GuzzleReplay\GuzzleReplay;

// create middleware either in recording or in replay mode
//$middleware = GuzzleReplay::create(Mode::Record);
$middleware = GuzzleReplay::create(Mode::Replay);
$client = new Client();
$middleware->inject($client);
$api = new GithubApi($client);
```

### Usage with Laravel Http Facade
```php
use Illuminate\Support\Facades\Http;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\Mode;
Http::globalMiddleware(GuzzleReplay::create(Mode::Replay));
```

### Always recording to file and not to a method
```php
use Holgerk\GuzzleReplay\FileRecorder;
use Holgerk\GuzzleReplay\Options;
Options::$globalRecorderFactory = fn() => new FileRecorder();
```

### Masking sensistive data
```php
$middleware = GuzzleReplay::create(Mode::Replay, Options::create()
    ->setRequestTransformer(static function (RequestModel $requestModel) {
        // mask authorization token, to not leak sensitive data
        $requestModel->replaceString($_ENV['GITHUB_TOKEN'], 'XXX');
        // or you can unset the header 
        //unset($requestModel->headers['Authorization']);
    })
);
```

### TODOS

- Document recorded request transformer
  - used to mask sensitive data
  - normalize host names between different environments (staging, production, etc.)
- Write documentation