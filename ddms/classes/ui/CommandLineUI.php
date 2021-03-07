<?php

namespace ddms\classes\ui;

use ddms\interfaces\ui\UserInterface;

class CommandLineUI implements UserInterface
{

    public function showMessage(string $message): void
    {
        echo $message;
    }

    public function showBanner(): void
    {
        $this->showMessage($this->getBanner());
    }

    private function getBanner():string
    {
        return
        "
      \e[0m\e[94m    _    _\e[0m
      \e[0m\e[93m __| |__| |_ __  ___\e[0m
      \e[0m\e[94m/ _` / _` | '  \(_-<\e[0m
      \e[0m\e[91m\__,_\__,_|_|_|_/__/\e[0m
      \e[0m\e[105m\e[30m  v0.0.3  \e[0m\e[0m\e[101m\e[30m  " . date('h:i:s A') . "  \e[0m";
    }

}
