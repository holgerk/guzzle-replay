<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use ReflectionClass;
use Symfony\Component\VarExporter\VarExporter;

class Recorder implements RecorderInterface
{
    private ?Recording $recording;
    private ?string $callerClass;
    private ?string $callerMethod;

    public function startRecord(): Recording
    {
        $this->recording = new Recording();
        $this->findCallingTestMethodAndClass();
        register_shutdown_function([$this, 'writeRecording']);
        return $this->recording;
    }

    public function startReplay(): Recording
    {
        $this->findCallingTestMethodAndClass();
        $class = new ReflectionClass($this->callerClass);
        $hasMethod = $class->hasMethod($this->getMethodWithRecording());
        $this->recording = (!$hasMethod)
            ? new Recording()
            : call_user_func($this->callerClass . '::' . $this->getMethodWithRecording());
        return $this->recording;
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
        $recording = VarExporter::export($recording->toArray());
        $recording = implode("\n            ", explode("\n", $recording));
        $methodString = <<<EOS
            public static function {$this->getMethodWithRecording()}(): \\$recordingClass
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
        return 'guzzleRecording_' . $this->callerMethod;
    }
}