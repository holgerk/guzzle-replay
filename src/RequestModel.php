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
            'uri' => $this->uri,
            'headers' => $this->headers,
            'body' => $this->body,
            'version' => $this->version,
        ];
    }
}