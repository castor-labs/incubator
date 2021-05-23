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
 * Class Headers.
 */
class Headers
{
    /**
     * @psalm-var array<string,list<string>>
     */
    private array $headers;

    /**
     * Headers constructor.
     */
    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    public static function fromMap(array $map): Headers
    {
        $headers = new self();
        foreach ($map as $key => $value) {
            $headers->add($key, $value);
        }

        return $headers;
    }

    public function all(): array
    {
        return $this->headers;
    }

    public function get(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function read(string $name): string
    {
        return $this->get($name)[0] ?? '';
    }

    public function add(string $name, string $part): void
    {
        $this->headers[strtolower($name)][] = $part;
    }

    public function put(string $name, string $part): void
    {
        $this->headers[strtolower($name)] = [$part];
    }

    public function contains(string $name, string $contents): bool
    {
        $values = $this->headers[strtolower($name)] ?? [];
        foreach ($values as $value) {
            if (true === str_contains($value, $contents)) {
                return true;
            }
        }

        return false;
    }

    public function has(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    public function toMap(): array
    {
        $map = [];
        foreach ($this->headers as $name => $entries) {
            foreach ($entries as $entry) {
                $map[$name] = $entry;
            }
        }

        return $map;
    }
}
