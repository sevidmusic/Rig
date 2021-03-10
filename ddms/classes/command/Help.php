<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;

class Help extends AbstractCommand implements Command
{

    public function run(UserInterface $ddmsUI, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $flags = $preparedArguments['flags'];
        if(!empty($flags) && !key_exists('help', $flags)) {
            return false;
        }
        $ddmsUI->showMessage($this->getHelpFileOutput($this->determineHelpFlagName($flags)));
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineHelpFlagName(array $flags): string
    {
        $firstHelpOption = ($flags['help'][0] ?? 'help');
        unset($flags['help']);
        $flagNames = array_keys($flags);
        $helpFlagName = ($flagNames[0] ?? $firstHelpOption);
        return (file_exists($this->determineHelpFilePath($helpFlagName)) ? $helpFlagName : 'help');
    }

    private function getHelpFileOutput(string $helpFlagName): string
    {
        if(file_exists($this->determineHelpFilePath($helpFlagName)))
        {
            $output = file_get_contents($this->determineHelpFilePath($helpFlagName));
        }
        return (isset($output) && is_string($output) ? PHP_EOL . $output . PHP_EOL : '');
    }

    private function determineHelpFilePath(string $helpFlagName): string
    {
        return str_replace('ddms/classes/command','helpFiles', __DIR__) . DIRECTORY_SEPARATOR . str_replace('--', '', $helpFlagName) . '.txt';
    }

}
