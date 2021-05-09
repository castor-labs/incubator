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

namespace Castor;

use SplFixedArray;

/**
 * Class Arr.
 *
 * @psalm-template T
 */
class Arr extends SplFixedArray
{
    /**
     * @param mixed ...$elements
     */
    public static function make(...$elements): Arr
    {
        return static::fromArray($elements);
    }

    /**
     * @param array $array
     * @param bool  $preserveKeys
     */
    public static function fromArray($array, $preserveKeys = false): Arr
    {
        $arr = new static(count($array));
        foreach ($array as $key => $item) {
            $arr[$key] = $item;
        }

        return $arr;
    }

    public function filter(callable $callback): Arr
    {
        $arr = [];
        foreach ($this as $key => $value) {
            if (true === $callback($value, $key)) {
                $arr[] = $callback;
            }
        }

        return self::fromArray($arr);
    }

    public function map(callable $callback): Arr
    {
        $arr = [];
        foreach ($this as $key => $value) {
            $arr[] = $callback($value, $key);
        }

        return self::fromArray($arr);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }
}
