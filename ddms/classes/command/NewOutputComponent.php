<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class NewOutputComponent extends AbstractCommand implements Command
{

    /**
     * @var UserInterface $currentUserInterface
     */
    private $currentUserInterface;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $this->currentUserInterface = $userInterface;
        ['flags' => $flags] = $this->validateArguments($preparedArguments);
        $this->showMessage('  Creating new OutputComponent, ' . $flags['name'][0] . ' for App ' . $flags['for-app'][0] . ' whose output will be:' . PHP_EOL . PHP_EOL . $this->filterOutput($flags) . PHP_EOL . PHP_EOL . '  Note: Single quotes (\') and backslashes (\) are escaped for assignment in OutputComponent\'s configuration file.' . PHP_EOL . PHP_EOL);
        $this->createFiles($flags);
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function createFiles(array $flags): void
    {
        file_put_contents(
            $this->pathToNewOutputComponent($flags),
            $this->generateOutputComponentFileContent($flags)
        );
        $this->showMessage(
            'Creating configuration file for new OutputComponent at ' .
            $this->pathToNewOutputComponent($flags)
        );
    }

    private function showMessage(string $message) : void
    {
        $this->currentUserInterface->showMessage(
            PHP_EOL .
            $message .
            PHP_EOL
        );
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function generateOutputComponentFileContent(array $flags): string
    {
        $template = strval(file_get_contents($this->pathToOutputComponentTemplate()));
         return str_replace(
            [
                '_NAME_',
                '_POSITION_',
                '_CONTAINER_',
                '_OUTPUT_',
            ],
            [
                $flags['name'][0],
                ($flags['position'][0] ?? '0'),
                ($flags['container'][0] ?? 'OutputComponents'),
                $this->filterOutput($flags)
            ],
            $template
        );
    }

    /**
     * @param array <string, array<int, string>> $flags
     */
    private function filterOutput(array $flags): string
    {
        return str_replace(["\\","'"], ["\\\\", "\'"], strval(implode(' ', ($flags['output'] ?? []))));
    }

    private function pathToOutputComponentTemplate(): string
    {
        $templatePath = str_replace('ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'OutputComponent.php';
        if(!file_exists($templatePath)) {
            throw new RuntimeException('Error: The OutputComponent.php file template is missing! You will not be able to create new OutputComponents until the OutputComponent.php template is restored at FileTemplates/OutputComponent.php');
        }
        return $templatePath;

    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function pathToNewOutputComponent(array $flags): string
    {
        return $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'OutputComponents' . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php';
    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function determineAppsOutputDirectoryPath(array $flags): string
    {
        return $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'Output';
    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function determineSharedOutputDirectoryPath(array $flags): string
    {
        return str_replace(['Apps', 'tmp'], 'SharedOutput', $flags['ddms-apps-directory-path'][0]);
    }
    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    private function validateArguments(array $preparedArguments): array
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new OutputComponent.');
        }
        if(!isset($flags['output'][0])) {
            throw new RuntimeException('  Please specify the output to assign to the new OutputComponent.');
        }
        if(!ctype_alnum($flags['name'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new OutputComponent.');
        }
        if(isset($flags['container'][0]) && !ctype_alnum($flags['container'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric container for the new OutputComponent.');
        }
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  Please specify the name of the App to create the new OutputComponent for');
        }
        if(!file_exists($this->determineAppDirectoryPath($flags)) || !is_dir($this->determineAppDirectoryPath($flags))) {
            throw new RuntimeException('  An App does not exist at' . $this->determineAppDirectoryPath($flags));
        }
        if(file_exists($this->pathToNewOutputComponent($flags))) {
            throw new RuntimeException('  Please specify a unique name for the new OutputComponent');
        }
        if(isset($flags['position'][0]) && !is_numeric($flags['position'][0])) {
            throw new RuntimeException('  Please specify a numeric position for the new OutputComponent. For example `--position 1`.');
        }
        return $preparedArguments;
    }

}
