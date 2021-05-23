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

namespace Castor\Net\Http;

use Castor\Str;
use Stringable;

/**
 * Class Query represents parsed query parameters.
 */
class Query implements Stringable
{
    /**
     * @psalm-param array<string,list<string>>
     */
    private array $params;

    /**
     * Query constructor.
     */
    protected function __construct(array $params)
    {
        $this->params = $params;
    }

    public function __toString(): string
    {
        return $this->toStr();
    }

    public function toStr(): string
    {
        $parts = [];
        foreach ($this->params as $key => $value) {
            foreach ($value as $item) {
                $parts[] = $key.'='.$item;
            }
        }

        return Str\join('&', ...$parts);
    }

    public static function create(): Query
    {
        return new self([]);
    }

    public static function parse(string $query): Query
    {
        $parsed = new self([]);
        if ('' === $query) {
            return $parsed;
        }
        foreach (\explode('&', $query) as $pair) {
            $pair = explode('=', $pair, 2);
            $parsed->add($pair[0], $pair[1] ?? '');
        }

        return $parsed;
    }

    public function all(): array
    {
        return $this->params;
    }

    public function get(string $param): array
    {
        return $this->params[$param] ?? [];
    }

    public function add(string $param, string $value): void
    {
        $this->params[urldecode($param)][] = urldecode($value);
    }

    public function put(string $param, string $value): void
    {
        $this->params[urldecode($param)] = [urldecode($value)];
    }

    public function has(string $param): bool
    {
        return array_key_exists($param, $this->params);
    }
}
