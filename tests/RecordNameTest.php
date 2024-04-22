<?php

namespace Holgerk\GuzzleReplay\Tests;

use Holgerk\GuzzleReplay\RecordName;
use PHPUnit\Framework\TestCase;
use function Holgerk\AssertGolden\assertGolden;

class RecordNameTest extends TestCase
{
    public function testInflect(): void
    {
        $recordName = RecordName::inflect();
        assertGolden('Holgerk\\GuzzleReplay\\Tests\\RecordNameTest', $recordName->getTestClassName());
        assertGolden('guzzleRecording_testInflect', $recordName->getShortName());
        assertGolden('RecordNameTest_testInflect_guzzleRecording', $recordName->getLongName());
    }

}
