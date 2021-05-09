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

use Stringable;

/**
 * Class Str.
 */
class Str implements Stringable
{
    public const TRIM_LEFT = 1;
    public const TRIM_RIGHT = 2;
    public const TRIM_BOTH = 3;

    protected string $string;

    /**
     * Str constructor.
     */
    protected function __construct(string $string)
    {
        $this->string = $string;
    }

    public function __toString(): string
    {
        return $this->string;
    }

    /**
     * @param Str ...$parts
     */
    public static function join(string $separator, Str ...$parts): Str
    {
        return new static(implode($separator, $parts));
    }

    /**
     * @return static
     */
    public static function make(string $str): Str
    {
        return new static($str);
    }

    public function index(string $substring): int
    {
        $pos = strpos($this->string, $substring);
        if (!is_int($pos)) {
            return -1;
        }

        return $pos;
    }

    public function extract(int $offset, int $length = null): Str
    {
        return self::make(substr($this->string, $offset, $length));
    }

    public function contains(string $substring): bool
    {
        return str_contains($this->string, $substring);
    }

    /**
     * @return $this
     */
    public function trim(string $chars, int $mode = self::TRIM_BOTH): Str
    {
        if (self::TRIM_LEFT === $mode) {
            return self::make(ltrim($this->string, $chars));
        }
        if (self::TRIM_RIGHT === $mode) {
            return self::make(rtrim($this->string, $chars));
        }
        if (self::TRIM_BOTH === $mode) {
            return self::make(trim($this->string, $chars));
        }

        return $this;
    }

    /**
     * @return Arr<Str>
     */
    public function split(string $separator, int $max = null): Arr
    {
        return Arr::fromArray(explode($separator, $this->string, $max));
    }

    public function match(string $pattern): Arr
    {
        $matches = [];
        preg_match('/'.$pattern.'/', $this->string, $matches);

        return Arr::fromArray($matches);
    }

    public function equals(string $string): bool
    {
        return $this->string === $string;
    }

    public function isEmpty(): bool
    {
        return '' === $this->string;
    }
}
