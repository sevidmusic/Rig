<?php

namespace Darling\Rig\classes\logs;

use \Darling\Rig\interfaces\logs\MessageLog as MessageLogInterface;
use \Darling\Rig\interfaces\messages\Message;

class MessageLog implements MessageLogInterface
{
    /** @var array<int, Message> $messages */
    private array $messages = [];

    public function __construct(Message ...$messages) {
        foreach($messages as $message) {
            $this->addMessage($message);
        }
    }

    /** @return array<int, Message> $messages */
    public function messages(): array
    {
        return $this->messages;
    }

    /**
     * Add a Message to this MessageLog.
     *
     * @param Message $message The Messsage to add.
     *
     * @return void
     *
     */
    public function addMessage(Message $message): void
    {
        $this->messages[] = $message;
    }
}

