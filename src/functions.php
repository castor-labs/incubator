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

namespace Castor\Str;

use function trim as php_trim;

const TRIM_BOTH = 0;
const TRIM_LEFT = 1;
const TRIM_RIGHT = 2;

/**
 * Returns the position of $searched in $subject.
 *
 * If $searched is not found, then -1 is returned.
 */
function index(string $subject, string $searched): int
{
    $pos = strpos($subject, $searched);
    if (!is_int($pos)) {
        return -1;
    }

    return $pos;
}

function contains(string $subject, string $containing): bool
{
    return str_contains($subject, $containing);
}

function startsWith(string $subject, string $starting): bool
{
    return str_starts_with($subject, $starting);
}

function endsWith(string $subject, string $ending): bool
{
    return str_ends_with($subject, $ending);
}

function trim(string $subject, string $chars, int $mode = TRIM_BOTH): string
{
    if (TRIM_BOTH === $mode) {
        return php_trim($subject, $chars);
    }
    if (TRIM_LEFT === $mode) {
        return ltrim($subject, $chars);
    }
    if (TRIM_RIGHT === $mode) {
        return rtrim($subject, $chars);
    }

    return $subject;
}

/**
 * @return string[]
 */
function matches(string $subject, string $pattern): array
{
    $matches = [];
    preg_match($pattern, $subject, $matches);

    return $matches;
}

function toCapital(string $subject): string
{
    return ucwords($subject);
}

function toUpper(string $subject): string
{
    return strtoupper($subject);
}

function toLower(string $subject): string
{
    return strtolower($subject);
}

/**
 * Replaces $search with $replacement on the $subject.
 */
function replace(string $subject, string $search, string $replacement): string
{
    return str_replace($subject, $search, $replacement);
}

/**
 * Slices a string.
 */
function slice(string $subject, int $offset, int $length = null): string
{
    $sub = substr($subject, $offset, $length ?? 0);
    if (!is_string($sub)) {
        return '';
    }

    return $sub;
}

function length(string $subject): int
{
    return strlen($subject);
}

/**
 * @return string[]
 */
function split(string $subject, string $separator, int $limit = null): array
{
    return explode($separator, $subject, $limit);
}

function join(string $separator, string ...$string): string
{
    return implode($separator, $string);
}

/**
 * @param mixed ...$parts
 */
function printf(string $template, ...$parts): string
{
    return sprintf($template, ...$parts);
}

namespace Castor\Arr;

use function sort as php_sort;
use function usort;

function map(array $array, callable $callback): array
{
    $mapped = [];
    foreach ($array as $key => $value) {
        $mapped[] = $callback($value, $key);
    }

    return $mapped;
}

function sort(array $array, callable $comparator = null): array
{
    $array = values($array);
    if (null !== $comparator) {
        usort($array, $comparator);
    } else {
        php_sort($array);
    }

    return $array;
}

function values($array): array
{
    return array_values($array);
}

function keys($array): array
{
    return array_keys($array);
}

/**
 * @param array ...$arrays
 */
function merge(array ...$arrays): array
{
    return array_merge(...$arrays);
}

function length(array $array): int
{
    return count($array);
}

/**
 * @param mixed $element
 */
function has(array $array, $element): bool
{
    return in_array($element, $array, true);
}

function unique(array $array): array
{
    return array_unique($array);
}

function reverse(array $array): array
{
    return array_reverse($array);
}

function filter(array $array, callable $callback): array
{
    $filtered = [];
    foreach ($array as $key => $value) {
        if (true === $callback($value, $key)) {
            $filtered[] = $value;
        }
    }

    return $filtered;
}
