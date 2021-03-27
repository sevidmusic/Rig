<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class AssignToResponse extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        $this->validateFlags($flags);
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function validateFlags(array $flags): void
    {
        if(!isset($flags['response'])) {
            throw new RuntimeException('  Please specify the name of the target --response.');
        }
        if(!isset($flags['for-app'])) {
            throw new RuntimeException('  Please specify the name of the App the Response or GlobalResponse belongs to.');
        }
        if(!file_exists($this->determineAppDirectoryPath($flags)) || !is_dir($this->determineAppDirectoryPath($flags))) {
            throw new RuntimeException('  An App does not exist at' . $this->determineAppDirectoryPath($flags));
        }
        if(!file_exists($this->determineResponsePath($flags)) || !is_file($this->determineResponsePath($flags))) {
            throw new RuntimeException('  An Response does not exist at' . $this->determineResponsePath($flags));
        }
        $this->validateSpecifiedComponents($flags);
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function validateSpecifiedComponents(array $flags): void
    {
        if(!isset($flags['requests'][0]) && !isset($flags['output-components'][0]) && !isset($flags['dynamic-output-components'][0])) {
            throw new RuntimeException('  You must specify at least one component to assign via --requests, --dynamic-output-components, or --output-components. For help use ddms --help --assign-to-response');
        }
        $this->verifySpecifiedComponentsExist($flags, 'requests', 'Requests');
        $this->verifySpecifiedComponentsExist($flags, 'output-components', 'OutputComponents');
        $this->verifySpecifiedComponentsExist($flags, 'dynamic-output-components', 'OutputComponents');
    }

     /**
     * @param array<string, array<int, string>> $flags
     */
    private function verifySpecifiedComponentsExist(array $flags, string $componentFlag, string $componentDirectoryName): void
    {
        if(isset($flags[$componentFlag])) {
            foreach($flags[$componentFlag] as $componentName) {
                $fileName = $componentName . '.php';
                if(!file_exists($this->determineComponentPath($componentName, $componentDirectoryName, $flags))) {
                    {
                        throw new RuntimeException(
                            '  A ' . $componentFlag  . ' does not exist at ' .
                            $this->determineComponentPath($componentName, $componentDirectoryName, $flags)
                        );
                    }
                }
            }
        }
    }

     /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineComponentPath(string $componentName, string $componentDirectoryName, array $flags): string {
        return $this->determineAppDirectoryPath($flags) . DIRECTORY_SEPARATOR . $componentDirectoryName . DIRECTORY_SEPARATOR . $componentName . '.php';
    }


    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineResponsePath($flags): string {
        return $this->determineAppDirectoryPath($flags) . DIRECTORY_SEPARATOR . 'Responses' . DIRECTORY_SEPARATOR . $flags['response'][0] . '.php';
    }
}
