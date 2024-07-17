# guzzle-replay middleware

Record guzzle requests and have them replayed during next runs.

### Usage with Laravel Http Facade
```php
use Illuminate\Support\Facades\Http;
use Holgerk\GuzzleReplay\GuzzleReplay;
use Holgerk\GuzzleReplay\Mode;
Http::globalMiddleware(GuzzleReplay::create(Mode::Replay));
```

### TODOS

- Document recorded request transformer
  - used to mask sensitive data
  - normalize host names between different environments (staging, production, etc.)
- Write documentation