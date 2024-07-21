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
    public const MODE_RECORD = Mode::Record;

    public const MODE_REPLAY = Mode::Replay;

    private function __construct(
        private readonly Mode $mode,
        private readonly Recording $recording,
        private readonly Options $options,
    ) {}

    public static function create(Mode $mode, ?Options $options = null): self
    {
        $options ??= self::makeOptions();
        if ($mode === Mode::Replay) {
            $recording = $options->recorder->startReplay($options->recordName);
        } else {
            $recording = $options->recorder->startRecord($options->recordName);
        }
        return new self($mode, $recording, $options);
    }

    public function inject(Client $client): self
    {
        /** @var HandlerStack $stack */
        $stack = $client->getConfig()['handler'];
        $stack->push($this);

        return $this;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    private static function makeOptions(): Options
    {
        return Options::create()->setRecordName(
            (Options::$globalRecordNameFactory ?? fn(int $distance) => RecordName::inflect($distance))(4)
        );
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
}