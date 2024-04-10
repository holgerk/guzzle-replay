<?php

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Response;
use JsonSerializable;

final class Recording implements JsonSerializable
{
    /** @var Record[] */
    private array $records = [];

    public static function fromJson(array $data): self
    {
        $self = new self();
        $self->records = array_map(fn (array $record) => Record::fromJson($record), $data['records']);
        return $self;
    }

    public function addRecord(Record $record): void
    {
        $this->records[] = $record;
    }

    public function jsonSerialize(): array
    {
        return [
            'records' => $this->records,
        ];
    }

    public function findResponse(RequestModel $requestModel): Response
    {
        foreach ($this->records as $record) {
            if ($record->requestModel == $requestModel) {
                return $record->responseModel->toResponse();
            }
        }
        throw new NoReplayFoundException();
    }
}