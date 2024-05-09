<?php

namespace Darling\Rig\classes\messages;

use \Darling\Rig\interfaces\messages\Message as MessageInterface;
use \Darling\PHPTextTypes\interfaces\strings\Text;
use \Darling\PHPTextTypes\classes\strings\Text as TextClass;

class Message extends TextClass implements MessageInterface, Text
{

}

