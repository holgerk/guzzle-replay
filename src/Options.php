<?php

declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

class Options
{
    /** @var ?callable(int $distance):RecordName */
    public static $globalRecordNameFactory = null;

    /** @var ?callable():RecorderInterface */
    public static $globalRecorderFactory = null;

    /** @var ?callable(RequestModel):void */
    public static $globalRequestTransformer = null;

    /** @var ?callable(ResponseModel):void */
    public static $globalResponseTransformer = null;

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
        $self->recordName = (
            self::$globalRecordNameFactory ?? fn(int $distance) => RecordName::inflect($distance)
        )(3);
        $self->recorder = (
            self::$globalRecorderFactory ?? fn() => new FileRecorder()
        )();
        $self->requestTransformer = self::$globalRequestTransformer ?? fn() => static function (RequestModel $_): void { /* noop */ };
        $self->responseTransformer = self::$globalResponseTransformer ?? fn() => static function (ResponseModel $_): void { /* noop */ };

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

    public static function resetGlobals(): void
    {
        static::$globalRecordNameFactory = null;
        static::$globalRecorderFactory = null;
        static::$globalRequestTransformer = null;
        static::$globalResponseTransformer = null;
    }
}