<?php

namespace Darling\Rig\tests\classes\messages;

use \Darling\Rig\classes\messages\Message;
use \Darling\Rig\tests\RigTest;
use \Darling\Rig\tests\interfaces\messages\MessageTestTrait;
use \PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Message::class)]
class MessageTest extends RigTest
{

    /**
     * The MessageTestTrait defines
     * common tests for implementations of the
     * Darling\Rig\interfaces\messages\Message
     * interface.
     *
     * @see MessageTestTrait
     *
     */
    use MessageTestTrait;

    public function setUp(): void
    {
        $this->setExpectedMessageString($this->randomString());
        $this->setMessageTestInstance(
            new Message($this->expectedMessageString())
        );
    }

}

