<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use rig\classes\command\NewRequest;
use rig\classes\command\NewResponse;
use rig\classes\command\NewOutputComponent;
use rig\classes\command\NewDynamicOutputComponent;
use rig\classes\command\AssignToResponse;
use rig\classes\ui\CommandLineUI;
use rig\interfaces\ui\UserInterface;
use \RuntimeException;
use tests\traits\TestsCreateApps;

final class AssignToResponseTest extends TestCase
{

    use TestsCreateApps;

    private const RESPONSES_POSITION_REGEX = '/[0-9][,]/';
    private const RESPONSES_POSITION_FOUND_VALUE = '$0';
    private string $appName;
    private string $requestName;
    private string $responseName;
    private UserInterface $ui;
    private AssignToResponse $assignToResponse;

    public function testRunThrowsExceptionIf_response_IsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsExceptionIf_for_app_IsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--response',
                    $this->responseName,
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedAppDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    self::getRandomAppName(),
                    '--response',
                    $this->responseName,
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfAtLeastOneComponentToBeAssignedIsNotSpecified() : void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--response',
                    $this->responseName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfSpecifiedResponseDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--response',
                    self::getRandomAppName(),
                    '--requests',
                    $this->requestName
                ]
            )
        );
    }

    public function testRunThrowsRuntimeExceptionIfAnyOfTheSpecifiedComponentsToBeAssignedDoNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->assignToResponse->run(
            $this->ui,
            $this->assignToResponse->prepareArguments(
                [
                    '--for-app',
                    $this->appName,
                    '--response',
                    $this->responseName,
                    '--requests',
                    $this->requestName,
                    '--output-components',
                    self::getRandomAppName()
                ]
            )
        );
    }

    public function testRunAssignsAllSpecifiedComponentsToSpecifiedResponse(): void
    {
        $preparedArguments = $this->assignToResponse->prepareArguments(
            [
                '--for-app',
                $this->appName,
                '--response',
                $this->responseName,
                '--requests',
                $this->requestName,
                $this->createTestRequestReturnName($this->appName, $this->ui),
                '--dynamic-output-components',
                $this->createTestDynamicOutputComponentReturnName($this->appName, $this->ui),
                $this->createTestDynamicOutputComponentReturnName($this->appName, $this->ui),
                 '--output-components',
                $this->createTestOutputComponentReturnName($this->appName, $this->ui),
                $this->createTestOutputComponentReturnName($this->appName, $this->ui),
                $this->createTestOutputComponentReturnName($this->appName, $this->ui),
            ]
        );
        $expectedContent = $this->expectedModifiedResponseContent($preparedArguments);
        $this->assignToResponse->run($this->ui, $preparedArguments);
        $this->assertEquals($expectedContent, $this->getResponseContent($preparedArguments));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedModifiedResponseContent(array $preparedArguments): string
    {
        $initialResponseContent = $this->getResponseContent($preparedArguments);
        $assignmentString = $this->expectedAssignments($preparedArguments);
        return strval(
            preg_replace(
                self::RESPONSES_POSITION_REGEX,
                self::RESPONSES_POSITION_FOUND_VALUE . $assignmentString,
                $initialResponseContent
            )
        );
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedAssignments(array $preparedArguments): string {
        $assignments = '';
        $assignments .= $this->generateAssignments($preparedArguments, 'Requests', 'requests');
        $assignments .= $this->generateAssignments($preparedArguments, 'OutputComponents', 'output-components');
        $assignments .= $this->generateAssignments($preparedArguments, 'OutputComponents', 'dynamic-output-components');
        return $assignments;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function generateAssignments(array $preparedArguments, string $componentDirName, string $flagName): string
    {
        $assignments = '';
        foreach($preparedArguments['flags'][$flagName] as $requestName) {
            $assignments .= PHP_EOL . $this->generateNewEntry($requestName, $preparedArguments, $componentDirName, $flagName);
        }
        return $assignments;
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function generateNewEntry(string $componentName, array $preparedArguments, string $componentDirName, string $flagName): string {
        return str_replace(
            [
                '_NAME_',
                '_TYPE_',
                '_CONTAINER_'
            ],
            [
                $componentName,
                $this->determineComponentType($flagName),
                $this->determineContainer($componentName, $preparedArguments, $componentDirName)
            ],
            $this->getResponseAssingmentTemplate()
        );
    }

    private function determineComponentType(string $flagName) : string
    {
        return str_replace([' ', 'ts'], ['', 't'], ucwords(str_replace('-', ' ', $flagName)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function determineContainer(string $componentName, array $preparedArguments, string $componentDirName): string {
        $componentContent = $this->getComponentContent($componentName, $preparedArguments, $componentDirName);
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
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getComponentContent(string $componentName, array $preparedArguments, string $componentDirName): string {
        return strval(file_get_contents($this->expectedComponentPath($componentName, $preparedArguments, $componentDirName)));
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function testResponseExists(array $preparedArguments): void
    {
        if(!file_exists($this->expectedResponsePath($preparedArguments))) {
            throw new RuntimeException('Error: Test Response could not be found at: ' . $this->expectedResponsePath($preparedArguments) . ', expected modified content could not be determined!');
        }
    }

    private function getResponseAssingmentTemplate(): string {
        return strval(file_get_contents($this->determineTemplateFilePath()));
    }

    private function determineTemplateFilePath(): string
    {
        return strval(
            realpath(
                str_replace(
                    'tests' . DIRECTORY_SEPARATOR . 'command',
                    'FileTemplates',
                    __DIR__
                ) . DIRECTORY_SEPARATOR . 'ResponseAssignment.php'
            )
        );
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function getResponseContent(array $preparedArguments) : string
    {
        $this->testResponseExists($preparedArguments);
        return strval(
            file_get_contents(
                $this->expectedResponsePath(
                    $preparedArguments
                )
            )
        );

    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedResponsePath(array $preparedArguments) : string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . 'Responses' . DIRECTORY_SEPARATOR . $this->responseName . '.php';
    }

    /**
     * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
     */
    private function expectedComponentPath(string $componentName, array $preparedArguments, string $componentDirName) : string
    {
        return self::expectedAppDirectoryPath($preparedArguments) . DIRECTORY_SEPARATOR . $componentDirName . DIRECTORY_SEPARATOR . $componentName . '.php';
    }

    private function createTestRequestReturnName(string $appName, UserInterface $ui): string
    {
        $requestName = $appName . 'TestRequest' . strval(rand(420, 4200));
        $newApp = new NewRequest();
        $newAppPreparedArguments = $newApp->prepareArguments(
            [
                '--name',
                $requestName,
                '--for-app',
                $appName,
                '--container',
                'TestRequests'
            ]
        );
        $newApp->run($ui, $newAppPreparedArguments);
        return $requestName;
    }

    private function createTestResponseReturnName(string $appName, UserInterface $ui): string
    {
        $responseName = self::getRandomAppName() . 'Response';
        $newApp = new NewResponse();
        $newAppPreparedArguments = $newApp->prepareArguments(
            [
                '--name',
                $responseName,
                '--for-app',
                $appName
            ]
        );
        $newApp->run($ui, $newAppPreparedArguments);
        return $responseName;
    }

    private function createTestOutputComponentReturnName(string $appName, UserInterface $ui): string
    {
        $outputComponentName = self::getRandomAppName() . 'OutputComponent';
        $newApp = new NewOutputComponent();
        $newAppPreparedArguments = $newApp->prepareArguments(
            [
                '--name',
                $outputComponentName,
                '--for-app',
                $appName,
                '--output',
                $outputComponentName . ' test output',
                '--container',
                'TestOutputComponents'
            ]
        );
        $newApp->run($ui, $newAppPreparedArguments);
        return $outputComponentName;
    }

    private function createTestDynamicOutputComponentReturnName(string $appName, UserInterface $ui): string
    {
        $dynamicOutputComponentName = self::getRandomAppName() . 'DynamicOutputComponent';
        $newApp = new NewDynamicOutputComponent();
        $newAppPreparedArguments = $newApp->prepareArguments(
            [
                '--name',
                $dynamicOutputComponentName,
                '--for-app',
                $appName,
                '--container',
                'TestDynamicOutputComponents'
            ]
        );
        $newApp->run($ui, $newAppPreparedArguments);
        return $dynamicOutputComponentName;
    }

    protected function setup(): void
    {
        $this->ui = new CommandLineUI();
        $this->appName = $this->createTestAppReturnName();
        $this->requestName = $this->createTestRequestReturnName(
            $this->appName,
            $this->ui
        );
        $this->responseName = $this->createTestResponseReturnName(
            $this->appName,
            $this->ui
        );
        $this->assignToResponse = new AssignToResponse();
    }

}

