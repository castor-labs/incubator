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

namespace Castor\Os;

use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use Traversable;

/**
 * Class Directory.
 */
class Directory implements IteratorAggregate
{
    /**
     * @var resource
     */
    private $resource;
    private Path $path;

    /**
     * Directory constructor.
     *
     * @param resource $resource
     */
    protected function __construct($resource, Path $path)
    {
        $this->resource = $resource;
        $this->path = $path;
    }

    public static function open(string $path): Directory
    {
        $osPath = Path::make($path);
        if (!$osPath->isDirectory()) {
            throw new InvalidArgumentException(sprintf('Path %s is not a directory', $path));
        }

        $resource = opendir($osPath->toStr());

        return new self($resource, $osPath);
    }

    public static function make(string $path, int $mode = 0777, bool $recursive = true): Directory
    {
        if (!mkdir($path, $mode, $recursive) && !is_dir($path)) {
            throw new RuntimeException('Could not create directory');
        }

        return static::open($path);
    }

    public static function put(string $path, int $mode = 0777, bool $recursive = true): Directory
    {
        if (is_dir($path)) {
            return static::open($path);
        }
        if (!is_dir($path) && !mkdir($path, $mode, $recursive) && !is_dir($path)) {
            throw new RuntimeException('Could not create directory');
        }

        return static::make($path);
    }

    public function getIterator(): Traversable
    {
        while (true) {
            $path = readdir($this->resource);
            if (!is_string($path)) {
                break;
            }
            if ('.' === $path) {
                yield clone $this->path;

                continue;
            }
            yield $this->path->join($path);
        }
    }
}
