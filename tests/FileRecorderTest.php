<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\FileRecorder;
use Holgerk\GuzzleReplay\Record;
use Holgerk\GuzzleReplay\RecordName;
use PHPUnit\Framework\TestCase;

class FileRecorderTest extends TestCase
{
    public function testRecord(): void
    {
        $recordingFile = __DIR__ . '/FileRecorderTest_testRecord_guzzleRecording.php';
        $expectedFile = __DIR__ . '/FileRecorderTest_testRecord_guzzleRecording.expected.php';
        if (file_exists($recordingFile)) {
            unlink($recordingFile);
        }
        $fileRecorder = new FileRecorder();
        $recording = $fileRecorder->startRecord(RecordName::inflect());
        $recording->addRecord(new Record(makeRequest(), makeResponse(['status' => 301])));
        $fileRecorder->writeRecording();

        self::assertFileEquals(
            $expectedFile,
            $recordingFile
        );

        // load recording
        $recording = $fileRecorder->startReplay(RecordName::inflect());
        self::assertCount(1, $recording->getRecords());
        self::assertEquals(301, $recording->getRecords()[0]->responseModel->status);
    }
}
