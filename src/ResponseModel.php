<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

final class ResponseModel
{
    public int $status;
    /** @var string[][] */
    public array $headers;
    public mixed $body;
    public string $version;
    public string $reason;

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

    /** @param array{status: int, headers: array, body: string, version: string, reason: string} $data */
    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->status = $data['status'];
        $self->headers = $data['headers'];
        $self->body = $data['body'];
        $self->version = $data['version'];
        $self->reason = $data['reason'];
        return $self;
    }

    public function toArray(): array
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