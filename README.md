# guzzle-replay middleware

Record guzzle requests and have them replayed during next runs.

### TODOS

- Add a possibility to normalize recorded requests
  - used to mask sensitive data
  - normalize host names between different environments (staging, production, etc.)
- Add a possibility to normalize recorded responses
  - used to format response body to make more readable 
- Write documentation