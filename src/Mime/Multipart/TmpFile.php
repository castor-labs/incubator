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

namespace Castor\Mime\Multipart;

use Castor\Io;
use Castor\Io\Error;

/**
 * Class TmpFile.
 */
final class TmpFile implements File
{
    use Io\ResourceHelper;

    /**
     * TmpFile constructor.
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public static function open(string $name): TmpFile
    {
        return new self(fopen($name, 'r+b'));
    }

    /**
     * @throws Io\Error
     */
    public function read(string &$bytes, int $length = Io\Reader::DEFAULT_READ_SIZE): int
    {
        return $this->innerRead($bytes, $length);
    }

    /**
     * @throws Io\Error
     */
    public function readAt(int $offset, string &$bytes, int $length = Io\Reader::DEFAULT_READ_SIZE): int
    {
        return $this->innerReadAt($offset, $bytes, $length);
    }

    /**
     * @throws Error
     */
    public function write(string $bytes): int
    {
        return $this->innerWrite($bytes);
    }

    public function writeAt(int $offset, string $bytes): int
    {
        return $this->innerWriteAt($offset, $bytes);
    }

    /**
     * @throws Io\Error
     */
    public function seek(int $offset = 0, int $whence = Io\Seeker::CURRENT): int
    {
        return $this->innerSeek($offset, $whence);
    }

    public function close(): void
    {
        $this->innerClose();
    }
}
