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
        public string $body,
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
    /**
     * @return array<string,mixed>
     */
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
    
    public function __toString(): string
    {
        $headers = json_encode($this->headers);
        return <<<EOS
        Request 
            status: $this->status
            headers: $headers
            body: $this->body
            version: $this->version
            reason: $this->reason
        EOS;
    }

    public function replaceString(string $search, string $replace): void
    {
        $this->body = str_replace($search, $replace, $this->body);
        foreach ($this->headers as &$values) {
            foreach ($values as &$value) {
                $value = str_replace($search, $replace, $value);
            }
        }
    }
}