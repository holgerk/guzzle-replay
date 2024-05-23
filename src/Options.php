<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

class Options
{
    public RecordName $recordName;

    public RecorderInterface $recorder;

    /** @var callable(RequestModel):void */
    public mixed $requestTransformer;

    /** @var callable(ResponseModel):void */
    public mixed $responseTransformer;

    public static function create(): self
    {
        $self = new self();

        // add defaults
        $self->recordName = RecordName::inflect(2);
        $self->recorder = new MethodRecorder();
        $self->requestTransformer = static function (RequestModel $_): void { /* noop */ };
        $self->responseTransformer = static function (ResponseModel $_): void { /* noop */ };

        return $self;
    }

    private function __construct() {}

    public function setRecordName(RecordName $recordName): self
    {
        $this->recordName = $recordName;
        return $this;
    }

    public function setRecorder(RecorderInterface $recorder): self
    {
        $this->recorder = $recorder;
        return $this;
    }

    /** @param callable(RequestModel):void $requestTransformer */
    public function setRequestTransformer(mixed $requestTransformer): self
    {
        $this->requestTransformer = $requestTransformer;
        return $this;
    }

    /** @param callable(ResponseModel):void $responseTransformer */
    public function setResponseTransformer(mixed $responseTransformer): self
    {
        $this->responseTransformer = $responseTransformer;
        return $this;
    }

}