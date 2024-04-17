<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Promise\FulfilledPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Middleware
{
    private Recording $recording;
    private Options $options;

    public static function create(Mode $mode, ?Options $options = null): self
    {
        $self = new self($mode);

        $self->options = $options ?? Options::create();

        if ($mode === Mode::Replay) {
            $self->recording = $self->options->recorder->startReplay();
        } else {
            $self->recording = $self->options->recorder->startRecord();
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

            ($this->options->requestTransformer)($requestModel);

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

    public function writeRecording(): void
    {
        $this->options->recorder->writeRecording();
    }


}