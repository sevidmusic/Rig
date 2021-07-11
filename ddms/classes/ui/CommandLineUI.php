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
  \e[0m\e[105m\e[30m  v1.0.7 \e[0m\e[0m\e[101m\e[30m  " . date('h:i:s A') . "  \e[0m" . PHP_EOL . PHP_EOL;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showOptions(array $arguments): void
    {
        $this->showMessage(PHP_EOL . '  Options:' . PHP_EOL);
        foreach($arguments['options'] as $key => $option) {
            $this->showMessage("\e[0m  \e[101m\e[30m $key \e[0m\e[105m\e[30m : \e[0m\e[104m\e[30m $option \e[0m" . PHP_EOL);
        }
        $this->showMessage(PHP_EOL);
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $arguments
     */
    public function showFlags(array $arguments): void
    {
        $this->showMessage('  Flags:' . PHP_EOL);
        foreach($arguments['flags'] as $key => $flags) {
            $this->showMessage("\e[0m  \e[101m\e[30m --$key \e[0m" . " : ");
            foreach($flags as $key => $flagArgument) {
                $this->showMessage("\e[0m\e[104m\e[30m $flagArgument \e[0m" . "  ");
            }
            $this->showMessage(PHP_EOL);
        }
    }

}
