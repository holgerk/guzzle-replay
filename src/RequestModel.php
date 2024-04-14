<?php

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Uri;
use JsonSerializable;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

final class RequestModel implements JsonSerializable
{
    private string $method;
    private UriInterface $uri;
    /** @var string[][] */
    private array $headers;
    private string $body;
    private string $version;

    public static function fromRequest(RequestInterface $request): self
    {
        $self = new self();
        $self->method = $request->getMethod();
        $self->uri = $request->getUri();
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
        $self->uri = new Uri($data['uri']);
        $self->headers = $data['headers'];
        $self->body = $data['body'];
        $self->version = $data['version'];
        return $self;
    }

    public function jsonSerialize(): array
    {
        return [
            'method' => $this->method,
            'uri' => (string) $this->uri,
            'headers' => $this->headers,
            'body' => $this->body,
            'version' => $this->version,
        ];
    }

    public function __toString(): string
    {
        return <<<EOS
        Request 
            method: $this->method
            uri: $this->uri
            headers: $this->headers
            body: $this->body
            version: $this->version
        EOS;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}