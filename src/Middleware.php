<?php

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Middleware
{
    private Recorder $recorder;

    public static function create(Mode $mode, ?Recorder $recorder = null): self
    {
        $self = new self($mode);
        $self->recorder ??= new Recorder();
        if ($mode === Mode::Replay) {
            $self->recorder->startReplay();
        } else {
            $self->recorder->startRecord();
        }
        return $self;
    }

    private function __construct(private readonly Mode $mode)
    {
    }

    public function __invoke(callable $next): callable
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $requestModel = RequestModel::fromRequest($request);
            if ($this->mode === Mode::Replay) {
                return new FulfilledPromise(
                    $this->recorder->findResponse($requestModel)
                );
            }
            return $next($request, $options)->then(
                function (ResponseInterface $response) use ($requestModel) {
                    $responseModel = ResponseModel::fromResponse($response);
                    $this->recorder->addRecord(new Record($requestModel, $responseModel));
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