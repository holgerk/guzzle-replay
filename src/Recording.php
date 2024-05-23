<?php
declare(strict_types=1);

namespace Holgerk\GuzzleReplay;

use GuzzleHttp\Psr7\Response;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class Recording
{
    /** @var Record[] */
    private array $records = [];

    /** @var array<string, bool> */
    private array $usedRecords = [];

    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->records = array_map(fn (array $record) => Record::fromArray($record), $data['records']);
        return $self;
    }

    public function addRecord(Record $record): void
    {
        $this->records[] = $record;
    }

    public function toArray(): array
    {
        return [
            'records' => array_map(fn (Record $r) => $r->toArray(), $this->records),
        ];
    }

    /** @psalm-suppress InvalidReturnType */
    public function getReplayResponse(RequestModel $requestModel): Response
    {
        if (empty($this->records)) {
            throw new NoRecordingExistsException(
                "\n| No records were found.\n"
                . "| Maybe you forgot to record.\n"
                . "| -> GuzzleReplay::inject(\$client, Mode::Record).\n"
            );
        }

        $foundRecord = null;
        foreach ($this->records as $record) {
            if ($record->requestModel == $requestModel) {
                $foundRecord = $record;
                $alreadyUsed = $this->usedRecords[spl_object_hash($record)] ?? false;
                if (! $alreadyUsed) {
                    break;
                }
            }
        }

        if ($foundRecord) {
            $alreadyUsed = $this->usedRecords[spl_object_hash($record)] ?? false;
            if ($alreadyUsed) {
                $this->throwReplayAlreadyUsedAssertionError($record->requestModel);
            }
            $this->usedRecords[spl_object_hash($foundRecord)] = true;
            return $foundRecord->responseModel->toResponse();
        }

        $this->throwNoReplayFoundAssertionError($requestModel);
    }

    private function throwReplayAlreadyUsedAssertionError(RequestModel $requestModel): void
    {
        $message = <<<EOS

        Replay for this request was already used:
        -----------------------------------------
        - $requestModel
        EOS;

        $message = implode("\n| ", explode("\n", $message));
        throw new ReplayAlreadyUsedAssertionError($message);
    }

    private function throwNoReplayFoundAssertionError(RequestModel $requestModel): void
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
        throw new NoReplayFoundAssertionError($message);
    }

    private function sortRecordsByDistanceToRequest(RequestModel $requestModel): array
    {
        $differenceByRecord = [];
        foreach ($this->records as $record) {
            $difference = 0;
            if ($requestModel->method != $record->requestModel->method) {
                $difference += levenshtein(
                    $requestModel->method,
                    $record->requestModel->method
                );
            }
            if ($requestModel->uri != $record->requestModel->uri) {
                $difference += levenshtein(
                    $requestModel->uri,
                    $record->requestModel->uri
                );
            }
            if ($requestModel->headers != $record->requestModel->headers) {
                $difference += levenshtein(
                    json_encode($requestModel->headers),
                    json_encode($record->requestModel->headers)
                );
            }
            if ($requestModel->body != $record->requestModel->body) {
                $difference += levenshtein(
                    $requestModel->body,
                    $record->requestModel->body
                );
            }
            if ($requestModel->version != $record->requestModel->version) {
                $difference += levenshtein(
                    $requestModel->version,
                    $record->requestModel->version
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

    public function getRecords(): array
    {
        return $this->records;
    }
}