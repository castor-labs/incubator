<?php

declare(strict_types=1);

/**
 * @project Castor Incubator
 * @link https://github.com/castor-labs/incubator
 * @package castor/incubator
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Os\Path;

use Castor\Arr;
use Castor\Str;
use function file_exists;
use function is_dir;
use function is_file;
use function pathinfo;

const SEPARATOR = DIRECTORY_SEPARATOR;

function join(string ...$parts): string
{
    $parts = Arr\map($parts, function (string $part, int $key) {
        if (0 === $key) {
            return $part;
        }

        return Str\trim($part, SEPARATOR, Str\TRIM_LEFT);
    });

    return Str\join(SEPARATOR, ...$parts);
}

function extension(string $path): string
{
    return pathinfo($path, PATHINFO_EXTENSION);
}

function filename(string $path): string
{
    return pathinfo($path, PATHINFO_FILENAME);
}

function basename(string $path): string
{
    return pathinfo($path, PATHINFO_BASENAME);
}

function dirname(string $path): string
{
    return pathinfo($path, PATHINFO_DIRNAME);
}

function isDirectory(string $path): bool
{
    return is_dir($path);
}

function isFile(string $path): bool
{
    return is_file($path);
}

function pathExists(string $path): bool
{
    return file_exists($path);
}

namespace Castor\Os;

/**
 * @psalm-return iterable<string>
 */
function glob(string $pattern, int $flags = 0): iterable
{
    $arr = \glob($pattern, $flags);
    if (!is_array($arr)) {
        throw new \InvalidArgumentException('Invalid glob pattern provided');
    }
    yield from $arr;
}

function tempPath(): string
{
    return sys_get_temp_dir();
}

/**
 * @noinspection NotOptimalIfConditionsInspection
 */
function ensureDir(string $path, int $perm = 0777): void
{
    if (!Path\isDirectory($path) && makeDir($path, $perm) && !Path\isDirectory($path)) {
        throw new \RuntimeException('Could not create directory');
    }
}

function makeDir(string $path, int $perm = 0777, bool $recursive = true): bool
{
    return mkdir($path, $perm, $recursive);
}

function remove(string $path): void
{
    unlink($path);
}
