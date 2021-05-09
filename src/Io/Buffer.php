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

namespace Castor\Io;

use Stringable;

/**
 * Class Buffer.
 */
final class Buffer implements ReadSeeker, ReaderAt, WriteSeeker, WriterAt, Stringable
{
    use ResourceHelper;

    /**
     * Buffer constructor.
     *
     * @param $resource
     */
    protected function __construct($resource)
    {
        $this->setResource($resource);
    }

    /**
     * @throws Error
     */
    public function __toString(): string
    {
        $this->seek(0, Seeker::START);

        return readAll($this);
    }

    /**
     * Creates an in-memory buffer of bytes.
     *
     * @throws Error
     */
    public static function from(string $string): Buffer
    {
        $buffer = new self(fopen('php://memory', 'w+b'));
        $buffer->write($string);

        return $buffer;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->innerRead($bytes, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function readAt(int $offset, string &$bytes = '', int $length = Reader::DEFAULT_READ_SIZE): int
    {
        return $this->innerReadAt($offset, $bytes, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset = 0, int $whence = Seeker::CURRENT): int
    {
        return $this->innerSeek($offset, $whence);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $bytes): int
    {
        return $this->innerWrite($bytes);
    }

    /**
     * {@inheritDoc}
     */
    public function writeAt(int $offset, string $bytes): int
    {
        return $this->innerWriteAt($offset, $bytes);
    }
}
