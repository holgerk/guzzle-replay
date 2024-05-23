<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

final class ResponseModel
{
    private function __construct(
        public int $status,
        /** @var string[][] */
        public array $headers,
        public mixed $body,
        public string $version,
        public string $reason,
    ) {}

    public static function fromResponse(ResponseInterface $response): self
    {
        $self = new self(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody()->getContents(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase(),
        );

        $response->getBody()->rewind();

        return $self;
    }

    /** @param array{status: int, headers: array, body: string, version: string, reason: string} $data */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['status'],
            $data['headers'],
            $data['body'],
            $data['version'],
            $data['reason'],
        );
    }

    public function toArray(): array
    {
        $result = [
            'status' => $this->status,
            'headers' => $this->headers,
            'body' => $this->body,
            'version' => $this->version,
            'reason' => $this->reason,
        ];

        $headerLookup = array_combine(
            array_map('strtolower', array_keys($this->headers)),
            array_keys($this->headers)
        );
        $contentType = $this->headers[$headerLookup['content-type'] ?? -1][0] ?? '';
        if (stripos($contentType, 'json') !== false) {
            $result['decodedBody'] = json_decode($this->body, true);
        }

        return $result;
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