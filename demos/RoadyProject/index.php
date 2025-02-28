<?php

use \Darling\PHPFileSystemPaths\classes\paths\PathToExistingDirectory as PathToExistingDirectoryInstance;
use \Darling\PHPTextTypes\classes\collections\SafeTextCollection as SafeTextCollectionInstance;
use \Darling\PHPTextTypes\classes\strings\SafeText as SafeTextInstance;
use \Darling\PHPTextTypes\classes\strings\Text as TextInstance;
use \Darling\RoadyModuleUtilities\classes\configuration\ModuleRoutesJsonConfigurationReader as ModuleRoutesJsonConfigurationReaderInstance;
use \Darling\RoadyModuleUtilities\classes\determinators\ModuleCSSRouteDeterminator as ModuleCSSRouteDeterminatorInstance;
use \Darling\RoadyModuleUtilities\classes\determinators\ModuleJSRouteDeterminator as ModuleJSRouteDeterminatorInstance;
use \Darling\RoadyModuleUtilities\classes\determinators\ModuleOutputRouteDeterminator as ModuleOutputRouteDeterminatorInstance;
use \Darling\RoadyModuleUtilities\classes\determinators\RoadyModuleFileSystemPathDeterminator as RoadyModuleFileSystemPathDeterminatorInstance;
use \Darling\RoadyModuleUtilities\classes\directory\listings\ListingOfDirectoryOfRoadyModules as ListingOfDirectoryOfRoadyModulesInstance;
use \Darling\RoadyModuleUtilities\classes\paths\PathToDirectoryOfRoadyModules as PathToDirectoryOfRoadyModulesInstance;
use \Darling\RoadyModuleUtilities\interfaces\paths\PathToDirectoryOfRoadyModules;
use \Darling\RoadyRoutes\classes\sorters\RouteCollectionSorter as RouteCollectionSorterInstance;
use \Darling\RoadyRoutingUtilities\classes\requests\Request as RequestInstance;
use \Darling\RoadyRoutingUtilities\classes\routers\Router as RouterInstance;
use \Darling\RoadyUIUtilities\classes\ui\html\UserInterface as UserInterfaceInstance;

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


function pathToDirectoryOfRoadyModules(): PathToDirectoryOfRoadyModules
{
    $roadysRootDirectory = __DIR__;
    $roadysRootDirectoryParts = explode(
        DIRECTORY_SEPARATOR,
        $roadysRootDirectory
    );
    $safeText = [];
    foreach ($roadysRootDirectoryParts as $pathPart) {
        if(!empty($pathPart)) {
            $safeText[] = new SafeTextInstance(
                new TextInstance($pathPart)
            );
        }
    }
    $safeText[] = new SafeTextInstance(
        new TextInstance('modules')
    );
    return new PathToDirectoryOfRoadyModulesInstance(
        new PathToExistingDirectoryInstance(
            new SafeTextCollectionInstance(...$safeText),
        ),
    );
}

$currentRequest = new RequestInstance();
$roadyModuleFileSystemPathDeterminator =
    new RoadyModuleFileSystemPathDeterminatorInstance();

$router = new RouterInstance(
    new ListingOfDirectoryOfRoadyModulesInstance(
        pathToDirectoryOfRoadyModules()
    ),
    new ModuleCSSRouteDeterminatorInstance(),
    new ModuleJSRouteDeterminatorInstance(),
    new ModuleOutputRouteDeterminatorInstance(),
    $roadyModuleFileSystemPathDeterminator,
    new ModuleRoutesJsonConfigurationReaderInstance(),
);

$response = $router->handleRequest($currentRequest);

$roadyUI = new UserInterfaceInstance(
    pathToDirectoryOfRoadyModules(),
    new RouteCollectionSorterInstance(),
    $roadyModuleFileSystemPathDeterminator,
);

echo $roadyUI->render($response);

