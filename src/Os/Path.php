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

use Castor\Net\Uri;
use Castor\Str;

/**
 * Class Path represents an operating system path.
 */
class Path extends Str
{
    public const SEPARATOR = DIRECTORY_SEPARATOR;

    public static function fromUriPath(Uri\Path $path): Path
    {
        return new static($path->replace(Uri\Path::SEPARATOR, self::SEPARATOR)->toStr());
    }

    /**
     * @param string ...$parts
     */
    public function join(string ...$parts): Path
    {
        $clone = clone $this;
        foreach ($parts as $part) {
            $clone->string .= self::SEPARATOR.ltrim($part, self::SEPARATOR);
        }

        return $clone;
    }

    public function toUriPath(): Uri\Path
    {
        return Uri\Path::fromOsPath($this);
    }

    public function exists(): bool
    {
        return file_exists($this->string);
    }

    public function getBasename(string $suffix = ''): string
    {
        return basename($this->string, $suffix);
    }

    public function getExtension(): string
    {
        return pathinfo($this->string, PATHINFO_EXTENSION);
    }

    public function getFilename(): string
    {
        return pathinfo($this->string, PATHINFO_FILENAME);
    }

    public function isAbsolute(): bool
    {
        return self::SEPARATOR === $this->string[0] || !$this->match('/^[a-zA-Z]\:[\/,\\\\].{1,}/')->isEmpty();
    }

    public function isFile(): bool
    {
        return is_file($this->string);
    }

    public function isDirectory(): bool
    {
        return is_dir($this->string);
    }

    public function getDirname(): string
    {
        return pathinfo($this->string, PATHINFO_DIRNAME);
    }
}
