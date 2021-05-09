<?php


namespace Castor\Io;

use Stringable;

/**
 * Class Buffer
 * @package Castor\Io
 */
final class Buffer implements ReadSeeker, ReaderAt, WriteSeeker, WriterAt, Stringable
{
    use ResourceHelper;

    /**
     * Creates an in-memory buffer of bytes
     *
     * @param string $string
     * @return Buffer
     * @throws Error
     */
    public static function from(string $string): Buffer
    {
        $buffer = new self(fopen('php://memory', 'w+b'));
        $buffer->write($string);
        return $buffer;
    }

    /**
     * Buffer constructor.
     * @param $resource
     */
    protected function __construct($resource)
    {
        $this->setResource($resource);
    }

    /**
     * @inheritDoc
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->innerRead($bytes, $length);
    }

    /**
     * @inheritDoc
     */
    public function readAt(int $offset, string &$bytes = '', int $length = Reader::DEFAULT_READ_SIZE): int
    {
        return $this->innerReadAt($offset, $bytes, $length);
    }

    /**
     * @inheritDoc
     */
    public function seek(int $offset, int $whence = Seeker::START): int
    {
        return $this->innerSeek($offset, $whence);
    }

    /**
     * @inheritDoc
     */
    public function write(string $bytes): int
    {
        return $this->innerWrite($bytes);
    }

    /**
     * @inheritDoc
     */
    public function writeAt(int $offset, string $bytes): int
    {
        return $this->innerWriteAt($offset, $bytes);
    }

    /**
     * @return string
     * @throws Error
     */
    public function __toString(): string
    {
        return readAll($this);
    }
}