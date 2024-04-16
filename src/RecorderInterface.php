<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

interface RecorderInterface
{
    public function startRecord(): Recording;

    public function startReplay(): Recording;

    public function writeRecording(): void;
}