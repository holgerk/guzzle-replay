<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

class Options
{
    public RecordName $recordName;

    public RecorderInterface $recorder;

    /** @var callable(RequestModel):void */
    public mixed $requestTransformer;

    public static function create(): self
    {
        $self = new self();

        // add defaults
        $self->recordName = RecordName::inflect(3);
        $self->recorder = new MethodRecorder();
        $self->requestTransformer = function (RequestModel $_) { /* noop */ };

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

}