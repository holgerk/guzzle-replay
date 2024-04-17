<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

final class Options
{
    public RecorderInterface $recorder;

    /** @var callable(RequestModel):void */
    public mixed $requestTransformer;

    public static function create(): self
    {
        $self = new self();
        $self->recorder = new Recorder();
        $self->requestTransformer = function (RequestModel $requestModel) { /* noop */ };
        return $self;
    }

    private function __construct() {}

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