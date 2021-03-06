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

/**
 * Class Context.
 */
class Context
{
    /**
     * @psalm-var array<string,mixed>
     */
    private array $data;

    /**
     * Context constructor.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    public function put(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @return null|mixed
     */
    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function all(): array
    {
        return $this->data;
    }
}
