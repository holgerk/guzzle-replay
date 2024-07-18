<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\FulfilledPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class GuzzleReplay
{
    private Recording $recording;
    private Options $options;

    public static function create(Mode $mode, ?Options $options = null): self
    {
        $self = new self($mode);
        $self->options = $options ?? self::makeOptions();
        $self->initializeRecording();

        return $self;
    }

    public function inject(Client $client): self
    {
        /** @var HandlerStack $stack */
        $stack = $client->getConfig()['handler'];
        $stack->push($this);

        return $this;
    }

    private static function makeOptions(): Options
    {
        return Options::create()->setRecordName(
            (Options::$globalRecordNameFactory ?? fn(int $distance) => RecordName::inflect($distance))(4)
        );
    }

    private function __construct(private readonly Mode $mode) {}

    private function initializeRecording(): void
    {
        if ($this->mode === Mode::Replay) {
            $this->recording = $this->options->recorder->startReplay($this->options->recordName);
        } else {
            $this->recording = $this->options->recorder->startRecord($this->options->recordName);
        }
    }
    
    public function __invoke(callable $next): callable
    {
        return function (RequestInterface $request, array $options) use ($next) {
            $requestModel = RequestModel::fromRequest($request);

            ($this->options->requestTransformer)($requestModel);

            if ($this->mode === Mode::Replay) {
                return new FulfilledPromise(
                    $this->recording->getReplayResponse($requestModel)
                );
            }
            return $next($request, $options)->then(
                function (ResponseInterface $response) use ($requestModel) {
                    $responseModel = ResponseModel::fromResponse($response);

                    ($this->options->responseTransformer)($responseModel);

                    $this->recording->addRecord(new Record($requestModel, $responseModel));
                    return $response;
                }
            );
        };
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

}