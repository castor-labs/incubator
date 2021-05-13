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
        return $this->toStr();
    }

    /**
     * @return $this
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
        $clone = clone $this;
        $clone->string .= substr($this->string, $offset, $length);

        return $clone;
    }

    public function contains(string $substring): bool
    {
        return str_contains($this->string, $substring);
    }

    public function prepend(string $string): Str
    {
        $clone = clone $this;
        $clone->string .= $string.$this->string;

        return $clone;
    }

    public function append(string $string): Str
    {
        $clone = clone $this;
        $clone->string .= $string;

        return $clone;
    }

    /**
     * @return $this
     */
    public function replace(string $search, string $replacement): Str
    {
        $clone = clone $this;
        $clone->string = str_replace($search, $replacement, $this->string);

        return $clone;
    }

    public function slice(int $offset, int $length = null): Str
    {
        $length = $length ?? ($this->length() - $offset);
        $clone = clone $this;
        $clone->string = substr($this->string, $offset, $length);

        return $clone;
    }

    public function toStr(): string
    {
        return $this->string;
    }

    /**
     * @return $this
     */
    public function trim(string $chars, int $mode = self::TRIM_BOTH): Str
    {
        $clone = clone $this;
        if (self::TRIM_LEFT === $mode) {
            $clone->string = ltrim($this->string, $chars);
        }
        if (self::TRIM_RIGHT === $mode) {
            $clone->string = rtrim($this->string, $chars);
        }
        if (self::TRIM_BOTH === $mode) {
            $clone->string = trim($this->string, $chars);
        }

        return $clone;
    }

    /**
     * @return Arr<Str>
     */
    public function split(string $separator, int $max = null): Arr
    {
        return Arr::fromArray(explode($separator, $this->string, $max))
            ->map(static fn (string $string) => new Str($string))
        ;
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

    public function length(): int
    {
        return strlen($this->string);
    }
}
