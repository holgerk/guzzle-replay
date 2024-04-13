<?php

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Middleware
{
    private Recorder $recorder;
    private Recording $recording;

    public static function create(Mode $mode, ?Recorder $recorder = null): self
    {
        $self = new self($mode);
        $self->recorder ??= new Recorder();
        if ($mode === Mode::Replay) {
            $self->setRecording($self->recorder->replay());
        } else {
            $self->recorder->record($self->getRecording());
        }
        return $self;
    }

    public function __construct(private readonly Mode $mode)
    {
        $this->recording = new Recording();
    }

    public function getRecording(): Recording
    {
        return $this->recording;
    }

    public function setRecording(Recording $recording): void
    {
        $this->recording = $recording;
    }

    public function __invoke(callable $next): callable
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $requestModel = RequestModel::fromRequest($request);
            if ($this->mode === Mode::Replay) {
                return new FulfilledPromise(
                    $this->recording->findResponse($requestModel)
                );
            }
            return $next($request, $options)->then(
                function (ResponseInterface $response) use ($requestModel) {
                    $responseModel = ResponseModel::fromResponse($response);
                    $this->recording->addRecord(new Record($requestModel, $responseModel));
                    return $response;
                }
            );
        };
    }

    public function writeRecording()
    {
        $this->recorder->writeRecording();
    }


}