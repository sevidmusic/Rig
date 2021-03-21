<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class NewDynamicOutputComponent extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $this->validateArguments($preparedArguments);
        $this->createFiles($flags);
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function createFiles(array $flags): void
    {
        file_put_contents(
            $this->pathToNewDynamicOutputComponent($flags),
            $this->generateDynamicOutputComponentFileContent($flags)
        );
        if(isset($flags['shared']) && !file_exists($this->determineSharedDynamicOutputDirectoryPath($flags))) {
           mkdir($this->determineSharedDynamicOutputDirectoryPath($flags));
        }
        file_put_contents(
            $this->pathToNewDynamicOutputFile($flags),
            ''
        );
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function generateDynamicOutputComponentFileContent(array $flags): string
    {
        $template = strval(file_get_contents($this->pathToDynamicOutputComponentTemplate()));
         return str_replace(
            [
                '_NAME_',
                '_POSITION_',
                '_CONTAINER_',
                '_FOR_APP_',
                '_DYNAMIC_OUTPUT_FILE_'
            ],
            [
                $flags['name'][0],
                ($flags['position'][0] ?? '0'),
                ($flags['container'][0] ?? 'DynamicOutputComponents'),
                $flags['for-app'][0],
                ($flags['file-name'][0] ?? $flags['name'][0] . '.php'),
            ],
            $template
        );
    }

    private function pathToDynamicOutputComponentTemplate(): string
    {
        $templatePath = str_replace('ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'DynamicOutputComponent.php';
        if(!file_exists($templatePath)) {
            throw new RuntimeException('Error: The DynamicOutputComponent.php file template is missing! You will not be able to create new DynamicOutputComponents until the DynamicOutputComponent.php template is restored at FileTemplates/DynamicOutputComponent.php');
        }
        return $templatePath;

    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function pathToNewDynamicOutputComponent(array $flags): string
    {
        return $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php';
    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function pathToNewDynamicOutputFile(array $flags): string
    {
        return (
            isset($flags['shared'])
            ? $this->determineSharedDynamicOutputDirectoryPath($flags) . DIRECTORY_SEPARATOR . ($flags['file-name'][0] ?? $flags['name'][0] . '.php')
            : $this->determineAppsDynamicOutputDirectoryPath($flags) . DIRECTORY_SEPARATOR . ($flags['file-name'][0] ?? $flags['name'][0] . '.php')
        );
    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function determineAppsDynamicOutputDirectoryPath(array $flags): string
    {
        return $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'DynamicOutput';
    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function determineSharedDynamicOutputDirectoryPath(array $flags): string
    {
        return str_replace('Apps', 'SharedDynamicOutput', $flags['ddms-apps-directory-path'][0]);
    }
    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    private function validateArguments(array $preparedArguments): array
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new DynamicOutputComponent.');
        }
        if(!ctype_alnum($flags['name'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new DynamicOutputComponent.');
        }
        if(isset($flags['container'][0]) && !ctype_alnum($flags['container'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new DynamicOutputComponent.');
        }
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  Please specify the name of the App to create the new DynamicOutputComponent for');
        }
        if(!file_exists($this->determineAppDirectoryPath($flags)) || !is_dir($this->determineAppDirectoryPath($flags))) {
            throw new RuntimeException('  An App does not exist at' . $this->determineAppDirectoryPath($flags));
        }
        if(file_exists($this->pathToNewDynamicOutputComponent($flags))) {
            throw new RuntimeException('  Please specify a unique name for the new DynamicOutputComponent');
        }
        if(isset($flags['position'][0]) && !is_numeric($flags['position'][0])) {
            throw new RuntimeException('  Please specify a numeric position for the new DynamicOutputComponent. For example `--position 1`.');
        }
        return $preparedArguments;
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function determineAppDirectoryPath(array $flags): string
    {
        return  $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0];
    }


}
