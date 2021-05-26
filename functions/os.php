<?php

declare(strict_types=1);

/**
 * @project Castor Incubator
 * @link https://github.com/castor-labs/incubator
 * @package castor/incubator
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Os;

use function copy as php_copy;

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

function copy(string $source, string $destination): void
{
    $ok = php_copy($source, $destination);
    if (false === $ok) {
        throw new \RuntimeException('Could not copy from source to destination');
    }
}
