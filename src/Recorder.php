<?php

namespace Holgerk\GuzzleReplay;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class Recorder
{


    private ?Recording $recording;
    private ?string $callerClass;
    private ?string $callerMethod;

    public function record(Recording $recording): void
    {
        $this->recording = $recording;
        register_shutdown_function([$this, 'writeRecording']);
        $this->findCallingTestMethodAndClass();
    }

    public function replay(): Recording
    {
        $this->findCallingTestMethodAndClass();
        $class = new ReflectionClass($this->callerClass);
        $hasMethod = $class->hasMethod($this->getMethodWithRecording());
        if (!$hasMethod) {
            return new Recording();
        }
        return call_user_func($this->callerClass . '::' . $this->getMethodWithRecording());
    }

    public function writeRecording(): void
    {
        if ($this->recording === null) {
            return;
        }
        $recording = $this->recording;
        $this->recording = null;

        $class = new ReflectionClass($this->callerClass);
        $hasMethod = $class->hasMethod($this->getMethodWithRecording());
        $lines = file($class->getFileName());
        $insertStartLine = null;
        $insertEndLine = null;
        if ($hasMethod) {
            $method = $class->getMethod($this->getMethodWithRecording());
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
        $recordingJson = json_encode($recording, JSON_PRETTY_PRINT);
        $recordingJson = implode("\n            ", explode("\n", $recordingJson));
        $methodString = <<<EOS
            public static function {$this->getMethodWithRecording()}(): \\$recordingClass
            {
                // GENERATED - DO NOT EDIT
                return \\$recordingClass::fromJson(json_decode(
                    <<<'_JSON_'
                    $recordingJson
                    _JSON_,
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ));
            }
        EOS;
        $linesToInsert = explode("\n", $methodString);
        $linesToInsert = array_map(fn ($line) => $line . "\n", $linesToInsert);
        // remove lines
        array_splice(
            $lines,
            $insertStartLine - 1,
            $insertEndLine - $insertStartLine + 1,
            $linesToInsert
        );
        file_put_contents($class->getFileName(), implode('', $lines));
    }

    private function findCallingTestMethodAndClass(): void
    {
        // find calling class and method
        $this->callerClass = null;
        $this->callerMethod = null;
        $stackItems = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($stackItems as $index => $stackItem) {
            if ($stackItem['class'] === Middleware::class) {
                $this->callerClass = $stackItems[$index + 1]['class'];
                $this->callerMethod = $stackItems[$index + 1]['function'];
                break;
            }
        }
        assert($this->callerClass !== null);
        assert($this->callerMethod !== null);
    }

    private function getMethodWithRecording(): string
    {
        $recordingMethod = $this->callerMethod . 'GuzzleRecording';
        return $recordingMethod;
    }
}