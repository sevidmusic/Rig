<?php

namespace ddms\classes\command;

use ddms\interfaces\command\Command;
use ddms\abstractions\command\AbstractCommand;
use ddms\interfaces\ui\UserInterface;
use \RuntimeException;

class NewRequest extends AbstractCommand implements Command
{

    public function run(UserInterface $userInterface, array $preparedArguments = ['flags' => [], 'options' => []]): bool
    {
        ['flags' => $flags] = $this->validateArguments($preparedArguments);
        $content = $this->generateRequestConfigContent($flags);
        $userInterface->showMessage(
            PHP_EOL .
            'Creating new Request for App ' . $flags['for-app'][0] . ' at ' . $this->pathToNewRequest($flags) .
            PHP_EOL .
            PHP_EOL
        );
        return boolval(file_put_contents($this->pathToNewRequest($flags), $content));
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function generateRequestConfigContent(array $flags): string
    {
        $template = strval(file_get_contents($this->pathToRequestTemplate()));
        return str_replace(['_NAME_', '_CONTAINER_', '_RELATIVE_URL_'], [$flags['name'][0], ($flags['container'][0] ?? 'Requests'), ($flags['relative-url'][0] ?? '')], $template);
    }

    private function pathToRequestTemplate(): string
    {
        $templatePath = str_replace('ddms' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'command', 'FileTemplates', __DIR__) . DIRECTORY_SEPARATOR . 'Request.php';
        if(!file_exists($templatePath)) {
            throw new RuntimeException('Error: The Request.php file template is missing! You will not be able to create new Requests until the Request.php template is restored at FileTemplates/Request.php');
        }
        return $templatePath;

    }

    /**
     * @param array<string,array<int,string>> $flags
     */
    private function pathToNewRequest(array $flags): string
    {
        return $flags['path-to-apps-directory'][0] . DIRECTORY_SEPARATOR . $flags['for-app'][0] . DIRECTORY_SEPARATOR . 'Requests' . DIRECTORY_SEPARATOR . $flags['name'][0] . '.php';
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     * @return array{"flags": array<string, array<int, string>>, "options": array<int, string>}
     */
    private function validateArguments(array $preparedArguments): array
    {
        ['flags' => $flags] = $preparedArguments;
        if(!isset($flags['name'][0])) {
            throw new RuntimeException('  Please specify a name for the new Request.');
        }
        if(!ctype_alnum($flags['name'][0])) {
            throw new RuntimeException('  Please specify an alphanumeric name for the new Request.');
        }
        if(!isset($flags['for-app'][0])) {
            throw new RuntimeException('  Please specify the name of the App to create the new Request for');
        }
        if(!file_exists($this->determineAppDirectoryPath($flags)) || !is_dir($this->determineAppDirectoryPath($flags))) {
            throw new RuntimeException('  An App does not exist at' . $this->determineAppDirectoryPath($flags));
        }
        if(file_exists($this->pathToNewRequest($flags))) {
            throw new RuntimeException('  Please specify a unique name for the new Request');
        }
        if(isset($flags['container'][0]) && !ctype_alnum($flags['container'][0])) {
            throw new RuntimeException('  Please specify a numeric container for the new Request. For example `--container 1`.');
        }
        if(isset($flags['relative-url'][0])) {
            $this->validateRelativeUrl($flags);
        }
        return $preparedArguments;
    }

    /**
     * @param array<string, array<int, string>> $flags
     */
    private function validateRelativeUrl(array $flags) : void
    {
        $tests = [
            substr($flags['relative-url'][0], 0,5) !== 'http:',
            substr($flags['relative-url'][0], 0,9) !== 'localhost',
            filter_var('http://testdom.ain/' . $flags['relative-url'][0], FILTER_VALIDATE_URL) !== false,
        ];
        if(in_array(false, $tests, true)) {
            throw new RuntimeException('  "' . $flags['relative-url'][0]  . '" is not a valid relative url. Please specify a valid relative url for the new Request. For example `index.php?foo=bar&baz=biz`.');
        }
    }

}
