<?php

namespace rig\classes\command;

use rig\interfaces\command\Command;
use rig\abstractions\command\AbstractCommand;
use rig\interfaces\ui\UserInterface;
use \RuntimeException;

class NewGlobalResponse extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $this->validateArguments($preparedArguments);
        $template = strval(file_get_contents($this->pathToGlobalResponseTemplate()));
        $content = str_replace(['_NAME_', '_POSITION_'], [$flags['name'][0], ($flags['position'][0] ?? '0')], $template);
        $userInterface->showMessage(
            PHP_EOL .
            'Creating new GlobalResponse for App ' . $flags['for-app'][0] . ' at ' . $this->pathToNewGlobalResponse($flags) .
            PHP_EOL .
            PHP_EOL
        );
        return boolval(file_put_contents($this->pathToNewGlobalResponse($flags), $content));
    }

    private function pathToGlobalResponseTemplate(): string
    {
        $templatePath = str_replace('rig' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'GlobalResponse.php';
        if(!file_exists($templatePath)) {
            throw new RuntimeException('Error: The GlobalResponse.php file template is missing! You will not be able to create new GlobalResponses until the GlobalResponse.php template is restored at FileTemplates/GlobalResponse.php');
        }
        return $templatePath;

    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function pathToNewGlobalResponse(array $flags): string
    {
        return $flags['path-to-apps-directory'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'Responses' . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php';
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    private function validateArguments(array $preparedArguments): array
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new GlobalResponse.');
        }
        if(!ctype_alnum($flags['name'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new GlobalResponse.');
        }
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  Please specify the name of the App to create the new GlobalResponse for');
        }
        if(!file_exists($this->determineAppDirectoryPath($flags)) || !is_dir($this->determineAppDirectoryPath($flags))) {
            throw new RuntimeException('  An App does not exist at' . $this->determineAppDirectoryPath($flags));
        }
        if(file_exists($this->pathToNewGlobalResponse($flags))) {
            throw new RuntimeException('  Please specify a unique name for the new GlobalResponse');
        }
        if(isset($flags['position'][0]) && !is_numeric($flags['position'][0])) {
            throw new RuntimeException('  Please specify a numeric position for the new GlobalResponse. For example `--position 1`.');
        }
        return $preparedArguments;
    }

}
