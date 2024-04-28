<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use ReflectionClass;
use Symfony\Component\VarExporter\VarExporter;

class FileRecorder implements RecorderInterface
{
    private ?Recording $recording;
    private ?RecordName $recordName;

    public function startRecord(RecordName $recordName): Recording
    {
        $this->recording = new Recording();
        $this->recordName = $recordName;
        register_shutdown_function([$this, 'writeRecording']);
        return $this->recording;
    }

    public function startReplay(RecordName $recordName): Recording
    {
        $this->recordName = $recordName;
        $recordingFileName = $this->getRecordingFileName();
        $recordingExists = file_exists($recordingFileName);
        $this->recording = (!$recordingExists)
            ? new Recording()
            : include $recordingFileName;
        return $this->recording;
    }

    public function writeRecording(): void
    {
        if ($this->recording === null) {
            return;
        }
        $recording = $this->recording;
        $this->recording = null;

        $recordingFileName = $this->getRecordingFileName();
        $recordingClass = Recording::class;
        $recording = VarExporter::export($recording->toArray());
        $recording = implode("\n    ", explode("\n", $recording));
        $fileContent = <<<EOS
        <?php
        // GENERATED - DO NOT EDIT
        return \\$recordingClass::fromArray(
            $recording
        );
        EOS;
        file_put_contents($recordingFileName, $fileContent);
    }

    private function getRecordingFileName(): string
    {
        $class = new ReflectionClass($this->recordName->getTestClassName());
        $dir = pathinfo($class->getFileName(), PATHINFO_DIRNAME);
        $recordingFileName = sprintf('%s/%s.php', $dir, $this->recordName->getLongName());
        return $recordingFileName;
    }

}