<?php

namespace Darling\Rig\tests\interfaces\messages;

use \Darling\Rig\interfaces\messages\Message;
use \PHPUnit\Framework\Attributes\CoversClass;

/**
 * The MessageTestTrait defines common tests for
 * implementations of the Message interface.
 *
 * @see Message
 *
 */
#[CoversClass(Message::class)]
trait MessageTestTrait
{

    private string $expectedMessageString = '';

    /**
     * @var Message $message An instance of a Message implementation
     *                       to test.
     */
    protected Message $message;

    /**
     * Set up an instance of a Message implementation to test.
     *
     * This method must set the Message implementation instance
     * to be tested via the setMessageTestInstance() method.
     *
     * This method may also be used to perform any additional setup
     * required by the implementation being tested.
     *
     * @return void
     *
     * @example
     *
     * ```
     * protected function setUp(): void
     * {
     *     $this->setExpectedMessageString($this->randomString());
     *     $this->setMessageTestInstance(
     *          new Message($this->expectedMessageString())
     *     );
     * }
     *
     * ```
     *
     */
    abstract protected function setUp(): void;

    /**
     * Return the Message implementation instance to test.
     *
     * @return Message
     *
     */
    protected function messageTestInstance(): Message
    {
        return $this->message;
    }

    /**
     * Set the Message implementation instance to test.
     *
     * @param Message $messageTestInstance An instance of an
     *                                     implementation of
     *                                     the Message interface
     *                                     to test.
     *
     * @return void
     *
     */
    protected function setMessageTestInstance(
        Message $messageTestInstance
    ): void
    {
        $this->message = $messageTestInstance;
    }


    /**
     * Set the string that is expected to be returned by
     * the Message implementation instance being tested's
     * __toString() method.
     *
     * @param string $expectedMessageString The string that is
     *                                      expected to be
     *                                      returned by the
     *                                      Message implementation
     *                                      instance being tested's
     *                                      __toString() method.
     *
     * @return void
     *
     */
    public function setExpectedMessageString(string $expectedMessageString): void
    {
        $this->expectedMessageString = $expectedMessageString;
    }

    /**
     * Return the string that is expected to be returned by
     * the Message implementation instance being tested's
     * __toString() method.
     *
     * @return string
     *
     */
    protected function expectedMessageString(): string
    {
        return $this->expectedMessageString;
    }

    /**
     * Test that the __toString() method returns the expected string.
     *
     * @return void
     *
     */
    public function test___toString_returns_expected_message(): void
    {
        $this->assertEquals(
            $this->expectedMessageString(),
            $this->messageTestInstance()->__toString(),
            $this->testFailedMessage(
                $this->messageTestInstance(),
                '__toString',
                'returns expected message'
            ),
        );
    }

    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;
}

