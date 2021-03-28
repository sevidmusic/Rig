<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class AssignToResponse extends AbstractCommand implements Command
{

    private const RESPONSES_POSITION_REGEX = '/[0-9][,]/';
    private const RESPONSES_POSITION_FOUND_VALUE = '$0';
    private UserInterface $ui;

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $preparedArguments;
        $this->ui = $userInterface;
        $this->validateFlags($flags);
        $this->assignToResponse($flags);
        return true;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function assignToResponse($flags): void
    {
        $newResponseContent = strval(
            preg_replace(
                self::RESPONSES_POSITION_REGEX,
                self::RESPONSES_POSITION_FOUND_VALUE . $this->getAssignments($flags),
                $this->getResponseContent($flags),
            )
        );
        file_put_contents($this->determineResponsePath($flags), $newResponseContent);
        $this->showMessage('Finished adding new assignments to ' . $flags['response'][0] . ' Response at path ' . $this->determineResponsePath($flags));
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function getAssignments(array $flags): string
    {
        $assignments = '';
        $this->showMessage($this->assingingToResponseMessage('Requests', $flags['response'][0]));
        $assignments .= $this->generateAssignments($flags, 'Requests', 'requests');
        $this->showMessage($this->assingingToResponseMessage('OutputComponents', $flags['response'][0]));
        $assignments .= $this->generateAssignments($flags, 'OutputComponents', 'output-components');
        $this->showMessage($this->assingingToResponseMessage('DynamicOutputComponents', $flags['response'][0]));
        $assignments .= $this->generateAssignments($flags, 'OutputComponents', 'dynamic-output-components');
        return $assignments;
    }

    private function assingingToResponseMessage(string $what, string $responseName): string
    {
        return 'Assigning '. $what . ' to ' . $responseName . ' Response.';
    }

    private function showMessage(string $message): void
    {
        $this->ui->showMessage(PHP_EOL . $message . PHP_EOL);
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function generateAssignments(array $flags, string $componentDirName, string $flagName): string
    {
        $assignments = '';
        foreach($flags[$flagName] as $componentName) {
            $this->showMessage($this->assingingToResponseMessage($componentName, $flags['response'][0]));
            $assignments .= $this->generateNewEntry($componentName, $flags, $componentDirName, $flagName);
        }
        return $assignments;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function generateNewEntry(string $componentName, array $flags, string $componentDirName, string $flagName): string {
        return str_replace(
            [
                '_NAME_',
                '_TYPE_',
                '_CONTAINER_'
            ],
            [
                $componentName,
                $this->determineComponentType($flagName),
                $this->determineContainer($componentName, $flags, $componentDirName)
            ],
            $this->getResponseAssingmentTemplate()
        );
    }

    private function getResponseAssingmentTemplate(): string {
        return strval(file_get_contents($this->determineTemplateFilePath()));
    }

    private function determineTemplateFilePath(): string
    {
        return strval(
            str_replace(
                'ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command',
                'FileTemplates',
                __DIR__
            ) . DIRECTORY_SEPARATOR . 'ResponseAssignment.php'
        );
    }

    private function determineComponentType(string $flagName) : string
    {
        return str_replace([' ', 'ts'], ['', 't'], ucwords(str_replace('-', ' ', $flagName)));
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function determineContainer(string $componentName, array $flags, string $componentDirName): string {
        $componentContent = $this->getComponentContent($componentName, $flags, $componentDirName);
        preg_match(
            "/[,]['][a-zA-Z0-9]+[']/",
            str_replace(
                [PHP_EOL, ' '],
                '',
                $componentContent
            ),
            $matches
        );
        return str_replace([',', "'"], '', ($matches[0] ?? 'ContainerUnknown'));
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function getComponentContent(string $componentName, array $flags, string $componentDirName): string {
        return strval(file_get_contents($this->expectedComponentPath($componentName, $flags, $componentDirName)));
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function expectedComponentPath(string $componentName, array $flags, string $componentDirName) : string
    {
        return $this->determineAppDirectoryPath($flags) . DIRECTORY_SEPARATOR . $componentDirName . DIRECTORY_SEPARATOR . $componentName . '.php';
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function getResponseContent(array $flags): string
    {
        return strval(file_get_contents($this->determineResponsePath($flags)));
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
