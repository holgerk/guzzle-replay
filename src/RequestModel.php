<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use JsonSerializable;
use Psr\Http\Message\RequestInterface;

final class RequestModel implements JsonSerializable
{
    public string $method;
    public string $uri;
    /** @var string[][] */
    public array $headers;
    public string $body;
    public string $version;

    public static function fromRequest(RequestInterface $request): self
    {
        $self = new self();
        $self->method = $request->getMethod();
        $self->uri = (string) $request->getUri();
        $self->headers = $request->getHeaders();
        $self->body = $request->getBody()->getContents();
        $self->version = $request->getProtocolVersion();
        return $self;
    }

    /** @param array{method: string, uri: string, headers: array, body: string, version: string} $data */
    public static function fromJson(array $data): self
    {
        $self = new self();
        $self->method = $data['method'];
        $self->uri = $data['uri'];
        $self->headers = $data['headers'];
        $self->body = $data['body'];
        $self->version = $data['version'];
        return $self;
    }

    public function jsonSerialize(): array
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
}