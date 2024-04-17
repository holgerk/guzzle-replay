<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

final class Record
{
    public function __construct(
        public RequestModel $requestModel,
        public ResponseModel $responseModel,
    ) {}

    public static function fromArray(array $record): self
    {
        return new self(
            RequestModel::fromArray($record['requestModel']),
            ResponseModel::fromArray($record['responseModel']),
        );
    }

    public function toArray(): array
    {
        return [
            'requestModel' => $this->requestModel->toArray(),
            'responseModel' => $this->responseModel->toArray(),
        ];
    }
}