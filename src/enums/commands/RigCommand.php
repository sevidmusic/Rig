<?php

namespace Darling\Rig\enums\commands;

enum RigCommand: string
{
    case DeleteRoute = 'delete-route';
    case Help = 'help';
    case ListRoutes = 'list-routes';
    case NewModule = 'new-module';
    case NewRoute = 'new-route';
    case StartServers = 'start-servers';
    case UpdateRoute = 'update-route';
    case Version = 'version';
    case ViewActionLog = 'view-action-log';
    case ViewReadme = 'view-readme';
}
