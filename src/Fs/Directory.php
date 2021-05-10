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

namespace Castor\Fs;

use Castor\Io;
use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use Traversable;

/**
 * Class Directory.
 */
class Directory implements IteratorAggregate
{
    private string $path;

    /**
     * Directory constructor.
     */
    protected function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function open(string $path): Directory
    {
        if (!self::exists($path)) {
            throw new InvalidArgumentException(sprintf('Path %s is not a directory', $path));
        }

        return new self($path);
    }

    public static function exists(string $path): bool
    {
        return is_dir($path);
    }

    public static function ensure(string $path, int $mode = 0777, bool $recursive = true): Directory
    {
        if (!is_dir($path) && !mkdir($path, $mode, $recursive) && !is_dir($path)) {
            throw new RuntimeException('Could not create directory');
        }

        return new self($path);
    }

    /**
     * @throws Io\Error
     */
    public function getIterator(): Traversable
    {
        $paths = scandir($this->path);
        if (!is_array($paths)) {
            throw new RuntimeException('Invalid directory provided');
        }
        foreach ($paths as $path) {
            if (self::exists($path)) {
                yield self::open($path);

                continue;
            }
            yield File::open($path);
        }
    }
}
