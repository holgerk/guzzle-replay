<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use Psr\Http\Message\RequestInterface;

final class RequestModel
{
    public function __construct(
        public string $method,
        public string $uri,
        /** @var string[][] */
        public array $headers,
        public string $body,
        public string $version,
    ) {}

    public static function fromRequest(RequestInterface $request): self
    {
        return new self(
            $request->getMethod(),
            (string) $request->getUri(),
            $request->getHeaders(),
            $request->getBody()->getContents(),
            $request->getProtocolVersion(),
        );
    }

    /** @param array{method: string, uri: string, headers: array, body: string, version: string} $data */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['method'],
            $data['uri'],
            $data['headers'],
            $data['body'],
            $data['version'],
        );
    }

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'uri' => $this->uri,
            'headers' => $this->headers,
            'body' => $this->body,
            'version' => $this->version,
        ];
    }

    public function __toString(): string
    {
        $headers = json_encode($this->headers);
        return <<<EOS
        Request 
            method: $this->method
            uri: $this->uri
            headers: $headers
            body: $this->body
            version: $this->version
        EOS;
    }

    public function replaceString(string $search, string $replace): void
    {
        $this->uri = str_replace($search, $replace, $this->uri);
        $this->body = str_replace($search, $replace, $this->body);
        foreach ($this->headers as &$values) {
            foreach ($values as &$value) {
                $value = str_replace($search, $replace, $value);
            }
        }
    }
}