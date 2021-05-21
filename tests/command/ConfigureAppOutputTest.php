<?php

namespace tests\command;

use PHPUnit\Framework\TestCase;
use ddms\classes\command\ConfigureAppOutput;
use ddms\classes\ui\CommandLineUI;
use ddms\interfaces\ui\UserInterface;
use tests\traits\TestsCreateApps;
use \RuntimeException;

final class ConfigureAppOutputTest extends TestCase
{

    use TestsCreateApps;
    private UserInterface $ui;
    private ConfigureAppOutput $configureAppOutput;

    private function getConfigureAppOutput(): ConfigureAppOutput
    {
        if(!isset($this->configureAppOutput)) {
            $this->configureAppOutput = new ConfigureAppOutput();
        }
        return $this->configureAppOutput;
    }

    private function getUserInterface(): UserInterface
    {
        if(!isset($this->ui)) {
            $this->ui = new CommandLineUI();
        }
        return $this->ui;
    }

    public function testRunThrowsRuntimeExceptionIfForAppIsNotSpecified(): void
    {
        $this->expectException(RuntimeException::class);
        $this->getConfigureAppOutput()->run($this->getUserInterface(), $this->getConfigureAppOutput()->prepareArguments(['--configure-app-output']));
    }
}
