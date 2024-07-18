# guzzle-replay middleware

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

### TODOS

- Document recorded request transformer
  - used to mask sensitive data
  - normalize host names between different environments (staging, production, etc.)
- Write documentation