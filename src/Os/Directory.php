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

use InvalidArgumentException;
use IteratorAggregate;
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
    private string $path;

    /**
     * Directory constructor.
     *
     * @param resource $resource
     */
    protected function __construct($resource, string $path)
    {
        $this->resource = $resource;
        $this->path = $path;
    }

    public static function open(string $path): Directory
    {
        if (!Path\isDirectory($path)) {
            throw new InvalidArgumentException(sprintf('Path %s is not a directory', $path));
        }

        $resource = opendir($path);

        return new self($resource, $path);
    }

    public static function make(string $path, int $mode = 0777): Directory
    {
        ensureDir($path, $mode);

        return static::open($path);
    }

    public static function put(string $path, int $mode = 0777): Directory
    {
        if (Path\isDirectory($path)) {
            return static::open($path);
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
            yield $path;
        }
    }
}
