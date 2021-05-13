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

namespace Castor\Net\Uri;

use Castor\Os;
use Castor\Str;

/**
 * Class Path represents hierarchical paths separated with forward
 * slashes ("/") like those founds in uris.
 *
 * It does not handle paths of operating systems. For that you must use the
 * Os\Path class.
 */
class Path extends Str
{
    public const SEPARATOR = '/';

    public static function fromOsPath(Os\Path $path): Path
    {
        return new static($path->replace(Os\Path::SEPARATOR, self::SEPARATOR)->toStr());
    }

    /**
     * @return static
     */
    public static function make(string $str): Str
    {
        $path = new static($str);

        return $path->replace(Os\Path::SEPARATOR, self::SEPARATOR);
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

    public function toOsPath(): Os\Path
    {
        return Os\Path::fromUriPath($this);
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
