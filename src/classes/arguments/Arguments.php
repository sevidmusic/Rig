<?php

namespace Darling\Rig\classes\arguments;

use \Darling\Rig\interfaces\arguments\Arguments as ArgumentsInterface;
use \Darling\Rig\enums\commands\RigCommand;
use \Darling\Rig\enums\commands\RigCommandArgument;

class Arguments implements ArgumentsInterface
{
    /** @return array<string, string> */
    public function asArray(): array
    {
        return [
            // Commands
            RigCommand::DeleteRoute->value => '',
            RigCommand::Help->value => '',
            RigCommand::ListRoutes->value => '',
            RigCommand::NewModule->value => '',
            RigCommand::NewRoute->value => '',
            RigCommand::StartServers->value => '',
            RigCommand::UpdateRoute->value => '',
            RigCommand::Version->value => '',
            RigCommand::ViewActionLog->value => '',
            RigCommand::ViewReadme->value => '',
            // Command Options
            RigCommandArgument::Authority->value => '',
            RigCommandArgument::DefinedForAuthorities->value => '',
            RigCommandArgument::DefinedForFiles->value => '',
            RigCommandArgument::DefinedForModules->value => '',
            RigCommandArgument::DefinedForNamedPositions->value => '',
            RigCommandArgument::DefinedForPositions->value => '',
            RigCommandArgument::DefinedForRequests->value => '',
            RigCommandArgument::ForAuthority->value => '',
            RigCommandArgument::ModuleName->value => '',
            RigCommandArgument::NamedPositions->value => '',
            RigCommandArgument::NoBoilerplate->value => '',
            RigCommandArgument::OpenInBrowser->value => '',
            RigCommandArgument::PathToRoadyProject->value => '',
            RigCommandArgument::Ports->value => '',
            RigCommandArgument::RelativePath->value => '',
            RigCommandArgument::RespondsTo->value => '',
            RigCommandArgument::RouteHash->value => '',
        ];
    }
}

