<?php

namespace ddms\interfaces\ui;

interface UserInterface {

    public const NOTICE = 'notice';
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const SUCCESS = 'success';

    public function notify(string $message, string $noticeType = self::NOTICE): void;

}
