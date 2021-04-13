<?php

/**
 * Components.php
 */

use DarlingDataManagementSystem\classes\component\Factory\App\AppComponentsFactory;

ini_set('display_errors', true);

require(
    strval(
        realpath(
            str_replace(
                'Apps' . DIRECTORY_SEPARATOR . strval(basename(__DIR__)),
                'vendor' . DIRECTORY_SEPARATOR . 'autoload.php',
                __DIR__
            )
        )
    )
);

function loadComponentConfigFiles(string $configurationDirectoryName, AppComponentsFactory $appComponentsFactory): void {
    $configurationDirectoryPath = __DIR__ . DIRECTORY_SEPARATOR . $configurationDirectoryName . DIRECTORY_SEPARATOR;
    foreach(array_diff(scandir($configurationDirectoryPath), array('.', '..')) as $file) {
        require $configurationDirectoryPath . $file;
    }
}

$specifiedDomain = ($argv[1] ?? '');

if(filter_var($specifiedDomain, FILTER_VALIDATE_URL)) {
    $useDomain = $argv[1];
}

$appComponentsFactory = new AppComponentsFactory(
    ...AppComponentsFactory::buildConstructorArgs(
    AppComponentsFactory::buildDomain(($useDomain ?? '_DOMAIN_'))
    )
);

loadComponentConfigFiles('OutputComponents', $appComponentsFactory);
loadComponentConfigFiles('Requests', $appComponentsFactory);
loadComponentConfigFiles('Responses', $appComponentsFactory);

$appComponentsFactory->buildLog(AppComponentsFactory::SHOW_LOG | AppComponentsFactory::SAVE_LOG);

