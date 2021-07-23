
function readServerLogFile(): string
{
    $log = (file_exists(getServerLogPath()) ? file_get_contents(getServerLogPath()) : '');
    return (is_string($log) ? $log : '');
}

function getServerLogPath(): string
{
    return '/tmp/rig-php-built-in-server.log';
}

/**
 * Get n lines starting at specified line number.
 * @param int $offset The offset to start at. Positive offsets correspond to lines
 *                    starting from first line. 0 and 1 both point to first line.
 *                    For example, the following do the same thing:
 *                      getLines($input, 0, 1);
 *                      getLines($input, 1, 1);
 *
 *                    Negative offsets correspond to lines starting from last line.
 *                    For example, to get just the last line, use either of the
 *                    following:
 *                      getLines($input, -1);
 *                      getLines($input, -1, 1);
 * @param int $limit The number of lines to return including the line at the specified
 *                   offset. If 0 is specified all lines starting from the line at the
 *                   specified offset will be returned.
 */
function getLines(string $input, int $offset, int $limit): string
{
    $offset = ($offset > 0 ? --$offset : $offset);
    $lines = array_filter(explode(PHP_EOL, $input));
    $lastLine = $lines[(count($lines) - 1)];
    $requestedLines = array_slice($lines, $offset, ($limit === 0 ? null : $limit));
    return implode(PHP_EOL, $requestedLines);
}

