<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

final class Options
{
    public RecorderInterface $recorder;

    /** @var callable(RequestModel):void */
    public mixed $requestNormalizer;

    public static function create(): self
    {
        $self = new self();
        $self->recorder = new Recorder();
        $self->requestNormalizer = function (RequestModel $requestModel) { /* noop */ };
        return $self;
    }

    private function __construct() {}

    public function setRecorder(RecorderInterface $recorder): self
    {
        $this->recorder = $recorder;
        return $this;
    }

    /** @param callable(RequestModel):void $requestNormalizer */
    public function setRequestNormalizer(mixed $requestNormalizer): self
    {
        $this->requestNormalizer = $requestNormalizer;
        return $this;
    }
}