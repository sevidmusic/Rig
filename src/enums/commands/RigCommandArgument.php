<?php

namespace Darling\Rig\enums\commands;

enum RigCommandArgument: string
{
    // Command Options
    case Authority = 'authority';
    case DefinedForAuthorities = 'defined-for-authorities';
    case DefinedForFiles = 'defined-for-files';
    case DefinedForModules = 'defined-for-modules';
    case DefinedForNamedPositions = 'defined-for-named-positions';
    case DefinedForPositions = 'defined-for-positions';
    case DefinedForRequests = 'defined-for-requests';
    case ForAuthority = 'for-authority';
    case ModuleName = 'module-name';
    case NamedPositions = 'named-positions';
    case NoBoilerplate = 'no-boilerplate';
    case OpenInBrowser = 'open-in-browser';
    case PathToRoadyProject = 'path-to-roady-project';
    case Ports = 'ports';
    case RelativePath = 'relative-path';
    case RespondsTo = 'responds-to';
    case RouteHash = 'route-hash';

}
