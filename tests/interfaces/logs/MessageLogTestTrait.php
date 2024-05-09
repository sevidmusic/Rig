<?php

namespace Darling\Rig\tests\interfaces\logs;

use Darling\Rig\classes\messages\Message;
use \Darling\Rig\interfaces\logs\MessageLog;
use \PHPUnit\Framework\Attributes\CoversClass;

/**
 * The MessageLogTestTrait defines common tests for
 * implementations of the MessageLog interface.
 *
 * @see MessageLog
 *
 */
#[CoversClass(MessageLog::class)]
trait MessageLogTestTrait
{

    /** @var array<int, Message> $expectedMessages */
    private array $expectedMessages;

    /**
     * @var MessageLog $messageLog
     *                              An instance of a
     *                              MessageLog
     *                              implementation to test.
     */
    protected MessageLog $messageLog;

    /**
     * Set up an instance of a MessageLog implementation to test.
     *
     * This method must set the MessageLog implementation instance
     * to be tested via the setMessageLogTestInstance() method.
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
     *     $expectedMessages = $this->randomArrayOfMessages();
     *     $this->setExpectedMessages($expectedMessages);
     *     $this->setMessageLogTestInstance(
     *         new MessageLog(...$expectedMessages)
     *     );
     * }
     *
     * ```
     *
     */
    abstract protected function setUp(): void;

    /**
     * Return the MessageLog implementation instance to test.
     *
     * @return MessageLog
     *
     */
    protected function messageLogTestInstance(): MessageLog
    {
        return $this->messageLog;
    }

    /**
     * Set the MessageLog implementation instance to test.
     *
     * @param MessageLog $messageLogTestInstance An instance of an
     *                                           implementation of
     *                                           the MessageLog
     *                                           interface to test.
     *
     * @return void
     *
     */
    protected function setMessageLogTestInstance(
        MessageLog $messageLogTestInstance
    ): void
    {
        $this->messageLog = $messageLogTestInstance;
    }

    /**
     * Generate a random Message.
     *
     * @return Message
     *
     */
    private function randomMessage(): Message
    {
        return new Message($this->randomString());
    }

    /**
     * Return an array of randomly generated Messages.
     *
     * @return array<int, Message>
     *
     */
    protected function randomArrayOfMessages(): array
    {
        $messages = [];
        while(rand(0, 100) < rand(0, 900)) {
            $messages[] = $this->randomMessage();
        }
        return $messages;
    }

    /**
     * Set the array of Messages that is expected to be returned
     * by the MessageLog implementation instance being tested's
     * messages() method.
     *
     * @param array<int, Message> $expectedMessages The array of
     *                                              Messages that
     *                                              is expected to
     *                                              be returned by
     *                                              the MessageLog
     *                                              implementation
     *                                              instance being
     *                                              tested's messages()
     *                                              method.
     *
     * @return void
     *
     */
    protected function setExpectedMessages(array $expectedMessages): void
    {
        $this->expectedMessages = $expectedMessages;
    }

    /**
     * Return the array of Messages that is expected to be returned
     * by the MessageLog implementation instance being tested's
     * messages() method.
     *
     * @return array<int, Message>
     *
     */
    protected function expectedMessages(): array
    {
        return $this->expectedMessages;
    }

    /**
     * Test that the messages() method returns the expected array
     * of Messages.
     *
     * @return void
     *
     */
    public function test_messages_returns_the_expected_array_of_messages(): void
    {
        $this->assertEquals(
            $this->expectedMessages(),
            $this->messageLogTestInstance()->messages(),
            $this->testFailedMessage(
                $this->messageLogTestInstance(),
                'messages',
                'returns expected array of messages'
            ),
        );
    }

    /**
     * Test addMessage() adds the specified message to the MessageLog.
     *
     * @return void
     *
     */
    public function test_addMessage_adds_the_specified_message_to_the_MessageLog(): void
    {
        $expectedMessages = $this->expectedMessages();
        for($i=0; $i < rand(1, 10); $i++) {
            $message = $this->randomMessage();
            $expectedMessages[] = $message;
            $this->messageLogTestInstance()->addMessage($message);
        }
        $this->setExpectedMessages($expectedMessages);
        $this->assertEquals(
            $this->expectedMessages(),
            $this->messageLogTestInstance()->messages(),
            $this->testFailedMessage(
                $this->messageLogTestInstance(),
                'messages',
                'returns expected array of messages'
            ),
        );
    }
    protected abstract function randomString(): string;
    abstract public static function assertEquals(mixed $expected, mixed $actual, string $message = ''): void;
    abstract protected function testFailedMessage(object $testedInstance, string $testedMethod, string $expectation): string;

}

