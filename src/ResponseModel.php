<?php

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Response;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface;

final class ResponseModel implements JsonSerializable
{
    private int $status;
    /** @var string[][] */
    private array $headers;
    private string $body;
    private string $version;
    private string $reason;

    public static function fromResponse(ResponseInterface $response): self
    {
        $self = new self();
        $self->status = $response->getStatusCode();
        $self->headers = $response->getHeaders();
        $self->body = $response->getBody()->getContents();
        $self->version = $response->getProtocolVersion();
        $self->reason = $response->getReasonPhrase();

        $response->getBody()->rewind();

        return $self;
    }

    public static function fromJson(array $data): self
    {
        $self = new self();
        $self->status = $data['status'];
        $self->headers = $data['headers'];
        $self->body = $data['body'];
        $self->version = $data['version'];
        $self->reason = $data['reason'];
        return $self;
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => $this->status,
            'headers' => $this->headers,
            'body' => $this->body,
            'version' => $this->version,
            'reason' => $this->reason,
        ];
    }

    public function toResponse(): Response
    {
        return new Response(
            status: $this->status,
            headers: $this->headers,
            body: $this->body,
            version: $this->version,
            reason: $this->reason,
        );
    }
}