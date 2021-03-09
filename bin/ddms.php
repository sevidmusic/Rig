<?php

require str_replace('bin', '', __DIR__) . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

use ddms\classes\ui\CommandLineUI;
use ddms\classes\command\Help;
use ddms\classes\command\DDMS;
use ddms\interfaces\command\Command;
use ddms\interfaces\ui\UserInterface;
use ddms\classes\factory\CommandFactory;

$ui = new CommandLineUI();
$ddms = new DDMS(new CommandFactory());

if($ui instanceof CommandLineUI) {
    $ui->showBanner();
}
try {
    $ddms->run($ui, $ddms->prepareArguments($argv));
} catch(\RuntimeException $ddmsError) {
    $ui->showMessage($ddmsError->getMessage());
}

mockStartServerRun($ui, $argv, $ddms);
mockViewServerLogRun($ui, $argv, $ddms);

/**
 * @param array<mixed> $argv
 */
function mockStartServerRun(UserInterface $ui, array $argv, Command $mockThis): void
{
    if(in_array('start-server', array_keys($mockThis->prepareArguments($argv)['flags']))) {
        startServer($mockThis->prepareArguments($argv), $ui);
    }
}

/**
 * @param array<mixed> $argv
 */
function mockViewServerLogRun(UserInterface $ui, array $argv, Command $mockThis): void
{
    if(in_array('view-server-log', array_keys($mockThis->prepareArguments($argv)['flags']))) {
        $ui->showMessage(PHP_EOL . "\e[0m  \e[105m\e[30mPhp Built In Server Log  \e[0m" . PHP_EOL);
        viewServerLog($mockThis->prepareArguments($argv), $ui);
    }
}



/**
 * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
 * &> /dev/null & xdg-open "http://localhost:${1:-8080}" &>/dev/null & disown
 */
function startServer(array $preparedArguments, UserInterface $ui): void
{
    ['flags' => $flags] = $preparedArguments;
    $serverLogPath = escapeshellarg('/tmp/ddms-php-built-in-server.log');
    $localhost = escapeshellarg('localhost:' . ($flags['port'][0] ?? strval(rand(8000, 8999))));
    $rootDirectory = escapeshellarg($flags['root-dir'][0] ?? escapeshellarg(__DIR__));
    $openInBrowser = (isset($flags['open-in-browser']) ? true : false);
    $domain = escapeshellarg('http://' . str_replace("'", '', $localhost));
    shell_exec(
        '/usr/bin/php -S ' . $localhost . ' -t ' . $rootDirectory . # start PHP built in server
        ' >> ' . $serverLogPath . # redirect sdout to server log
        ' 2>> ' . $serverLogPath . # redirect sderr to server log
        ' & sleep .09' . # give server a momement, this also allows --view-server-log to read log right away
        ($openInBrowser ? ' & xdg-open ' . $domain . ' &> /dev/null' : '') . # open in browsr if --open-in-browser flag specified
        ' & disown' # send all to bg and disown
    );
    $ui->showMessage(
        PHP_EOL . "\e[0m    \e[92mStarting development server\e[0m" . PHP_EOL .
        "\e[0m    \e[30m\e[105mPort:\e[0m \e[93m$domain\e[0m" . PHP_EOL .
        "\e[0m    \e[30m\e[105mRoot directory:\e[0m \e[95m$rootDirectory\e[0m" . PHP_EOL
    );
}

/**
 * @param array{"flags": array<string, array<int, string>>, "options": array<int, string>} $preparedArguments
 */
function viewServerLog(array $preparedArguments, UserInterface $ui): void
{
    ['flags' => $flags] = $preparedArguments;
    $offset = intval(($flags['view-server-log'][0] ?? 0));
    $numberOfLines = intval(($flags['view-server-log'][1] ?? 0));
    $log = (file_exists(getServerLogPath()) ? file_get_contents(getServerLogPath()) : '');
    $logLines = (is_string($log) && !empty($log) ? str_replace('[', '  [', $log) : getServerLogEmptyMessage());
    $ui->showMessage(getLines($logLines, $offset, $numberOfLines));
}

function getServerLogPath(): string
{
    return '/tmp/ddms-php-built-in-server.log';
}
function getServerLogEmptyMessage(): string
{
    return PHP_EOL . "\e[0m  \e[106m\e[30mServer log is empty\e[0m" . PHP_EOL;
}
/**
 * Get n lines starting at specified line number.
 * @param int $offset The offset to start at. Negative offsets will start at last line.
 * @param int $numberOfLines The number of lines to return including the starting line.
 *                           If 0 is specified all lines will be returned.
 */
function getLines(string $input, int $offset, int $numberOfLines): string
{
    $offset = ($offset !== 0 ? --$offset : $offset);
    $lines = explode(PHP_EOL, $input);
    $lastLine = $lines[(count($lines) - 2)];
    $requestedLines = array_slice($lines, $offset, ($numberOfLines === 0 ? null : $numberOfLines));
    return implode(PHP_EOL, $requestedLines);
}

