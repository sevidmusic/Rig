<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;
class NewApp extends AbstractCommand implements Command
{

    /**
     * @var UserInterface $ui
     */
    private $ui;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        $this->ui = $userInterface;
        ['flags' => $flags] = $preparedArguments;
        if(!in_array('name', array_keys($flags))) {
            throw new RuntimeException('  You must specify a name for the new App');
        }
        if(is_dir($this->pathToAppDirectory($flags))) {
            throw new RuntimeException('An App named ' .  $flags['name'][0] . ' already exists. Please specify a unique name.');
        }
        $this->createAppsDirectoryStructure($flags);
        $this->createAppsComponentsPhp($flags);
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function pathToAppDirectory(array $flags): string
    {
        return $flags['ddms-apps-directory-path'][0] . DIRECTORY_SEPARATOR . $flags['name'][0];
    }

    private function showMessage(string $message): void
    {
        $this->ui->showMessage(
            PHP_EOL .
            $message .
            PHP_EOL .
            PHP_EOL
        );
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function createAppsComponentsPhp(array $flags): void
    {
        $componentPhpFileTemplatePath = str_replace(
            'ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command',
            'FileTemplates' . DIRECTORY_SEPARATOR . 'Components.php',
            __DIR__
        );
        $componentsPhpTemplate = strval(file_get_contents($componentPhpFileTemplatePath));
        $content = str_replace('_DOMAIN_', $this->determineDomain($flags), $componentsPhpTemplate);
        $componentsPhpPath = $this->pathToAppDirectory($flags) . DIRECTORY_SEPARATOR . 'Components.php';
        $this->showMessage('Creating new Components.php for App ' . $flags['name'][0] . ' at ' . $componentsPhpPath);
        file_put_contents($componentsPhpPath, $content);
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineDomain(array $flags): string
    {
        if(isset($flags['domain']) && isset($flags['domain'][0]) && filter_var($flags['domain'][0], FILTER_VALIDATE_URL)) {
            return $flags['domain'][0];
        }
        return 'http://localhost:8080/';
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function createAppsDirectoryStructure(array $flags): void {
            $this->showMessage('Creating new App ' . $flags['name'][0] . ' at ' . $this->pathToAppDirectory($flags));
            mkdir($this->pathToAppDirectory($flags));

            $this->showMessage('Creating new css directory for App ' . $flags['name'][0] . ' at ' . $this->determineAppSubDirPath('css', $flags));
            mkdir($this->determineAppSubDirPath('css', $flags));

            $this->showMessage('Creating new js directory for App ' . $flags['name'][0] . ' at ' . $this->determineAppSubDirPath('js', $flags));
            mkdir($this->determineAppSubDirPath('js', $flags));

            $this->showMessage('Creating new DynamicOutput directory for App ' . $flags['name'][0] . ' at ' . $this->determineAppSubDirPath('DynamicOutput', $flags));
            mkdir($this->determineAppSubDirPath('DynamicOutput', $flags));

            $this->showMessage('Creating new resources directory for App ' . $flags['name'][0] . ' at ' . $this->determineAppSubDirPath('resources', $flags));
            mkdir($this->determineAppSubDirPath('resources', $flags));

            $this->showMessage('Creating new Responses directory for App ' . $flags['name'][0] . ' at ' . $this->determineAppSubDirPath('Responses', $flags));
            mkdir($this->determineAppSubDirPath('Responses', $flags));

            $this->showMessage('Creating new Requests directory for App ' . $flags['name'][0] . ' at ' . $this->determineAppSubDirPath('Requests', $flags));
            mkdir($this->determineAppSubDirPath('Requests', $flags));

            $this->showMessage($this->creatingDirectoryMessage('OutputComponents', $flags['name'][0], $this->determineAppSubDirPath('OutputComponents', $flags)));
            mkdir($this->determineAppSubDirPath('OutputComponents', $flags));
    }

    private function creatingDirectoryMessage(string $dirName, string $appName, string $path) : string
    {
        return "Creating new $dirName directory for App $appName at $path";
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineAppSubDirPath(string $name, array $flags) : string
    {
        return $this->pathToAppDirectory($flags) . DIRECTORY_SEPARATOR . $name;
    }

}

