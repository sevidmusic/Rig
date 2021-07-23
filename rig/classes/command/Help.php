<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;

class Help extends AbstractCommand implements Command
{

    public function run(UserInterface $rigUI, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $flags = $preparedArguments['flags'];
        if(!empty($flags) && !key_exists('help', $flags)) {
            return false;
        }
        $rigUI->showMessage($this->getHelpFileOutput($this->determineHelpFlagName($flags)));
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineHelpFlagName(array $flags): string
    {
        $firstFlagNamePassedToHelpFlag = ($flags['help'][0] ?? null);
        $flagNames = array_keys($flags);
        $firstFlagFollowingHelpFlag = ($flagNames[1] ?? '');
        $helpFlagName = ($firstFlagNamePassedToHelpFlag ?? $firstFlagFollowingHelpFlag);
        if($helpFlagName === 'path-to-apps-directory' && $firstFlagNamePassedToHelpFlag !== 'path-to-apps-directory') {
            unset($helpFlagName);
        }
        return (!empty($helpFlagName) && file_exists($this->determineHelpFilePath($helpFlagName)) ? $helpFlagName : 'help');
    }

    private function getHelpFileOutput(string $helpFlagName): string
    {
        return ($this->getHelpFileContents($helpFlagName) ?? '');
    }

    private function getHelpFileContents(string $helpFlagName): string|null
    {
        if(file_exists($this->determineHelpFilePath($helpFlagName)))
        {
            $output = file_get_contents($this->determineHelpFilePath($helpFlagName));
            return (is_string($output) ? $output : null);
        }
        return null;
    }


    private function determineHelpFilePath(string $helpFlagName): string
    {
        return str_replace('rig/classes/command','helpFiles', __DIR__) . DIRECTORY_SEPARATOR . str_replace('--', '', $helpFlagName) . '.txt';
    }

}
