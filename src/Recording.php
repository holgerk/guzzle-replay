<?php

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Response;
use JsonSerializable;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class Recording implements JsonSerializable
{
    /** @var Record[] */
    private array $records = [];

    public static function fromJson(array $data): self
    {
        $self = new self();
        $self->records = array_map(fn (array $record) => Record::fromJson($record), $data['records']);
        return $self;
    }

    public function addRecord(Record $record): void
    {
        $this->records[] = $record;
    }

    public function jsonSerialize(): array
    {
        return [
            'records' => $this->records,
        ];
    }

    public function findResponse(RequestModel $requestModel): Response
    {
        if (empty($this->records)) {
            throw new NoRecordingExistsException(
                "\n| No records were found.\n"
                . "| Maybe you forgot to record.\n"
                . "| -> Middleware::create(Mode::Record).\n"
            );
        }
        // TODO throw if record already used

        foreach ($this->records as $record) {
            if ($record->requestModel == $requestModel) {
                return $record->responseModel->toResponse();
            }
        }

        $this->throwNoReplayFoundException($requestModel);
    }

    private function throwNoReplayFoundException(RequestModel $requestModel): void
    {
        // no matching response found, create a helpful exception
        $sortedRecords = $this->sortRecordsByDistanceToRequest($requestModel);

        $builder = new UnifiedDiffOutputBuilder(
            "--- Expected\n+++ Actual\n",
            false
        );

        $differ = new Differ($builder);
        $diff = trim($differ->diff((string)$sortedRecords[0]->requestModel, (string)$requestModel));

        $message = <<<EOS

        No replay found for this request:
        ---------------------------------
        - $requestModel
        
        Diff to best matching expected request:
        ---------------------------------------
        $diff

        All expected requests (sorted by difference):
        ---------------------------------------------
        EOS;
        foreach ($sortedRecords as $record) {
            $message .= <<<EOS

            - $record->requestModel
            
            EOS;
        }
        $message = implode("\n| ", explode("\n", $message));
        throw new NoReplayFoundException($message);
    }

    private function sortRecordsByDistanceToRequest(RequestModel $requestModel): array
    {
        $differenceByRecord = [];
        foreach ($this->records as $record) {
            $difference = 0;
            if ($requestModel->getMethod() != $record->requestModel->getMethod()) {
                $difference += levenshtein(
                    $requestModel->getMethod(),
                    $record->requestModel->getMethod()
                );
            }
            if ((string)$requestModel->getUri() != (string)$record->requestModel->getUri()) {
                $difference += levenshtein(
                    (string)$requestModel->getUri(),
                    (string)$record->requestModel->getUri()
                );
            }
            if ($requestModel->getHeaders() != $record->requestModel->getHeaders()) {
                $difference += levenshtein(
                    json_encode($requestModel->getHeaders()),
                    json_encode($record->requestModel->getHeaders())
                );
            }
            if ($requestModel->getBody() != $record->requestModel->getBody()) {
                $difference += levenshtein(
                    $requestModel->getBody(),
                    $record->requestModel->getBody()
                );
            }
            if ($requestModel->getVersion() != $record->requestModel->getVersion()) {
                $difference += levenshtein(
                    $requestModel->getVersion(),
                    $record->requestModel->getVersion()
                );
            }
            $differenceByRecord[spl_object_id($record)] = $difference;
        }
        $sortedRecords = $this->records;
        usort($sortedRecords, fn(Record $a, Record $b) =>
            $differenceByRecord[spl_object_id($a)] <=> $differenceByRecord[spl_object_id($b)]
        );
        return $sortedRecords;
    }
}