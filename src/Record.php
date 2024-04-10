<?php

namespace Holgerk\GuzzleReplay;

use JsonSerializable;

class Record implements JsonSerializable
{
    public function __construct(
        public RequestModel $requestModel,
        public ResponseModel $responseModel,
    ) {}

    public static function fromJson(array $record): self
    {
        return new self(
            RequestModel::fromJson($record['requestModel']),
            ResponseModel::fromJson($record['responseModel']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'requestModel' => $this->requestModel,
            'responseModel' => $this->responseModel,
        ];
    }
}