<?php

namespace Darling\Rig\tests\classes\logs;

use \Darling\Rig\classes\logs\MessageLog;
use \Darling\Rig\tests\RigTest;
use \Darling\Rig\tests\interfaces\logs\MessageLogTestTrait;
use \PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MessageLog::class)]
class MessageLogTest extends RigTest
{

    /**
     * The MessageLogTestTrait defines
     * common tests for implementations of the
     * Darling\Rig\interfaces\logs\MessageLog
     * interface.
     *
     * @see MessageLogTestTrait
     *
     */
    use MessageLogTestTrait;

    public function setUp(): void
    {
        $expectedMessages = $this->randomArrayOfMessages();
        $this->setExpectedMessages($expectedMessages);
        $this->setMessageLogTestInstance(
            new MessageLog(...$expectedMessages)
        );
    }
}

