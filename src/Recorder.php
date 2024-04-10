<?php

namespace Holgerk\GuzzleReplay;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class Recorder
{


    private ?Recording $recording;
    private ?string $testClass;
    private ?string $testMethod;

    public function record(Recording $recording): void
    {
        $this->recording = $recording;
        register_shutdown_function([$this, 'writeRecording']);
        $this->findCallingTestMethodAndClass();

    }

    public function replay(): Recording
    {
        $this->findCallingTestMethodAndClass();
        $class = new ReflectionClass($this->testClass);
        $hasMethod = $class->hasMethod($this->getMethodWithRecording());
        if (!$hasMethod) {
            return new Recording();
        }
        return call_user_func($this->testClass . '::' . $this->getMethodWithRecording());
    }

    private function writeRecording(): void
    {
        assert($this->recording !== null);
        $class = new ReflectionClass($this->testClass);
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
        $recordingJson = json_encode($this->recording, JSON_PRETTY_PRINT);
        $recordingJson = implode("\n                ", explode("\n", $recordingJson));
        $methodString = <<<EOS
        
            public static function {$this->getMethodWithRecording()}(): \\$recordingClass
            {
                // generated - do not edit
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
        // find calling test method
        $this->testClass = null;
        $this->testMethod = null;
        foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $stackItem) {
            if (is_subclass_of($stackItem['class'], TestCase::class)) {
                $this->testClass = $stackItem['class'];
                $this->testMethod = $stackItem['function'];
                break;
            }
        }
        assert($this->testClass !== null);
        assert($this->testMethod !== null);
    }

    private function getMethodWithRecording(): string
    {
        $recordingMethod = $this->testMethod . 'GuzzleRecording';
        return $recordingMethod;
    }
}