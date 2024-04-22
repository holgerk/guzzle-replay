<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\RecorderInterface;
use Holgerk\GuzzleReplay\Recording;
use Holgerk\GuzzleReplay\RecordName;

class TestRecorder implements RecorderInterface
{
    private Recording $recording;

    public function startRecord(RecordName $recordName): Recording
    {
        return $this->recording = new Recording();
    }

    public function startReplay(RecordName $recordName): Recording
    {
        return $this->recording;
    }

    public function writeRecording(): void
    {
        // noop
    }

    public function getRecording(): Recording
    {
        return $this->recording;
    }
}