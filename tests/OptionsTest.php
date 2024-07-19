<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\FileRecorder;
use Holgerk\GuzzleReplay\MethodRecorder;
use Holgerk\GuzzleReplay\Options;
use Holgerk\GuzzleReplay\RecordName;
use Holgerk\GuzzleReplay\RequestModel;
use Holgerk\GuzzleReplay\ResponseModel;
use PHPUnit\Framework\TestCase;
use function Holgerk\AssertGolden\assertGolden;

class OptionsTest extends TestCase
{
    protected function tearDown(): void 
    {
        parent::tearDown();
        Options::resetGlobals();
    }

    public function testGlobalRecordNameFactory(): void
    {
        assertGolden('guzzleRecording_testGlobalRecordNameFactory', Options::create()->recordName->getShortName());

        Options::$globalRecordNameFactory = fn() => RecordName::make('class', 'method');
        assertGolden('guzzleRecording_method', Options::create()->recordName->getShortName());

        Options::resetGlobals();
        assertGolden('guzzleRecording_testGlobalRecordNameFactory', Options::create()->recordName->getShortName());
    }

    public function testGlobalRecorderFactory(): void
    {
        assertGolden(MethodRecorder::class, get_class(Options::create()->recorder));
        Options::$globalRecorderFactory = fn() => new FileRecorder();
        assertGolden(FileRecorder::class, get_class(Options::create()->recorder));
    }

    public function testGlobalRequestTransformer(): void
    {
        $request = makeRequest();
        (Options::create()->requestTransformer)($request);
        assertGolden('GET', $request->method);
        Options::$globalRequestTransformer = fn(RequestModel $r) => $r->method = 'POST';
        (Options::create()->requestTransformer)($request);
        assertGolden('POST', $request->method);
    }

    public function testGlobalResponseTransformer(): void
    {
        $response = makeResponse();
        (Options::create()->responseTransformer)($response);
        assertGolden(200, $response->status);
        Options::$globalResponseTransformer = fn(ResponseModel $r) => $r->status = 300;
        (Options::create()->responseTransformer)($response);
        assertGolden(300, $response->status);
    }    
}
