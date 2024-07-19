<?php

declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use ReflectionClass;
use Symfony\Component\VarExporter\VarExporter;

class MethodRecorder implements RecorderInterface
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
        $class = new ReflectionClass($this->recordName->getTestClassName());
        $hasMethod = $class->hasMethod($this->recordName->getShortName());
        $this->recording = (! $hasMethod)
            ? new Recording()
            : call_user_func($this->recordName->getTestClassName() . '::' . $this->recordName->getShortName());
        return $this->recording;
    }

    public function writeRecording(): void
    {
        if ($this->recording === null) {
            return;
        }
        $recording = $this->recording;
        $this->recording = null;

        $class = new ReflectionClass($this->recordName->getTestClassName());
        $hasMethod = $class->hasMethod($this->recordName->getShortName());
        $lines = file($class->getFileName());
        $insertStartLine = null;
        $insertEndLine = null;
        if ($hasMethod) {
            $method = $class->getMethod($this->recordName->getShortName());
            $insertStartLine = $method->getStartLine();
            $insertEndLine = $method->getEndLine();
        } else {
            foreach (array_reverse($lines, true) as $index => $line) {
                if (str_contains($line, '}')) {
                    $insertStartLine = $index + 1;
                    $insertEndLine = $index;
                    break;
                }
            }
        }
        $recordingClass = Recording::class;
        $recording = VarExporter::export($recording->toArray());
        $recording = implode("\n            ", explode("\n", $recording));
        $methodString = <<<EOS
            public static function {$this->recordName->getShortName()}(): \\$recordingClass
            {
                // GENERATED - DO NOT EDIT
                return \\$recordingClass::fromArray(
                    $recording
                );
            }
        EOS;
        $linesToInsert = explode("\n", $methodString);
        $linesToInsert = array_map(fn($line) => $line . "\n", $linesToInsert);
        // remove lines
        array_splice(
            $lines,
            $insertStartLine - 1,
            $insertEndLine - $insertStartLine + 1,
            $linesToInsert
        );
        file_put_contents($class->getFileName(), implode('', $lines));
    }
}