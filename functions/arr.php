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

namespace Castor\Arr;

use function sort as php_sort;
use Traversable;
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

function fromIter(Traversable $iter): array
{
    return iterator_to_array($iter);
}
