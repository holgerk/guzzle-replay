<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

interface RecorderInterface
{
    public function startRecord(RecordName $recordName): Recording;

    public function startReplay(RecordName $recordName): Recording;

    public function writeRecording(): void;
}