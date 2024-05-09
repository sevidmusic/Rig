<?php

namespace Darling\Rig\interfaces\logs;

use \Darling\Rig\interfaces\messages\Message;

/**
 * A MessageLog is a collection of Messages.
 *
 * Messages may be added to the log via the addMessage() method,
 * but may not be editied or removed.
 *
 */
interface MessageLog
{

    /**
     * Return an array of the Messages assigned to this MessageLog.
     *
     * @return array<int, Message>
     *
     */
    public function messages(): array;



    /**
     * Add a Message to this MessageLog.
     *
     * @param Message $message The Message to add.
     *
     * @return void
     *
     */
    public function addMessage(Message $message): void;

}

