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
     * @return $this
     */
    public static function make(string $str): Str
    {
        $path = new static($str);

        return $path->replace(Uri\Path::SEPARATOR, self::SEPARATOR);
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

    public function getBasename(): string
    {
        return pathinfo($this->string, PATHINFO_BASENAME);
    }

    public function getExtension(): string
    {
        return pathinfo($this->string, PATHINFO_EXTENSION);
    }

    public function getFilename(): string
    {
        return pathinfo($this->string, PATHINFO_FILENAME);
    }

    public function getDirname(): string
    {
        return pathinfo($this->string, PATHINFO_DIRNAME);
    }
}
