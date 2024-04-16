<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\RecorderInterface;
use Holgerk\GuzzleReplay\Recording;

class TestRecorder implements RecorderInterface
{
    private Recording $recording;

    public function startRecord(): Recording
    {
        return $this->recording = new Recording();
    }

    public function startReplay(): Recording
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