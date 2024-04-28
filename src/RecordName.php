<?php

namespace Holgerk\GuzzleReplay;

/**
 * long name consists of test class, test method name and suffix
 * short name consists of test method name and suffix
 */
class RecordName
{
    private string $testClassName;
    private string $testMethodName;
    private string $suffix = 'guzzleRecording';
    private string $prefix = 'guzzleRecording';

    private function __construct() {}

    public static function inflect($distance = 1): static
    {
        $self = new self();
        $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1 + $distance);
        $caller = end($stack);
        $self->testClassName = $caller['class'];
        $self->testMethodName = $caller['function'];
        return $self;
    }

    public static function make(string $class, string $method): static
    {
        $self = new self();
        $self->testClassName = $class;
        $self->testMethodName = $method;
        return $self;
    }

    public function getShortName(): string
    {
        return $this->prefix . '_' . $this->testMethodName;
    }

    public function getLongName(): string
    {
        $classBaseName = basename(str_replace('\\', '/', $this->testClassName));
        return $classBaseName . '_' . $this->testMethodName . '_' . $this->suffix;
    }

    public function getTestClassName(): string
    {
        return $this->testClassName;
    }
}