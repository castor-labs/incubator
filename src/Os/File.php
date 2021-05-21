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

use Castor\Io;
use Castor\Mime;
use function Castor\Os\Path\extension;
use function Castor\Os\Path\isFile;

/**
 * Class File represents an operating system file.
 */
final class File implements Io\ReadSeeker, Io\WriteSeeker, Io\ReaderAt, Io\WriterAt, Io\WriterTo, Io\Sizer, Mime\Type
{
    use Io\ResourceHelper;
    private string $path;

    /**
     * File constructor.
     *
     * @param resource $resource
     *
     * @throws Io\Error
     */
    protected function __construct($resource, string $path)
    {
        $this->setResource($resource);
        if (!stream_is_local($this->resource)) {
            throw new Io\Error(sprintf('The file %s is not a local file', $path));
        }
        $this->path = $path;
        $this->resource = $resource;
    }

    /**
     * @throws Error
     */
    public static function open(string $path): File
    {
        if (!isFile($path)) {
            throw new Error(sprintf('File %s does not exist', $path));
        }
        $resource = fopen($path, 'r+b');

        return new self($resource, $path);
    }

    public static function put(string $path): File
    {
        $resource = fopen($path, 'w+b');

        return new self($resource, $path);
    }

    /**
     * @throws Error
     */
    public static function make(string $path): File
    {
        if (isFile($path)) {
            throw new Error(sprintf('File %s already exists', $path));
        }
        $resource = fopen($path, 'x+b');

        return new self($resource, $path);
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
    public function readAt(int $offset, string &$bytes, int $length = Io\Reader::DEFAULT_READ_SIZE): int
    {
        return $this->innerReadAt($offset, $bytes, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function seek(int $offset = 0, int $whence = self::CURRENT): int
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

    /**
     * {@inheritDoc}
     */
    public function writeTo(Io\Writer $writer): int
    {
        return Io\copy($this, $writer);
    }

    public function size(): int
    {
        return filesize($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMimeType(): string
    {
        return Mime\DefaultRegistry::get()
            ->getMimeType(extension($this->path)) ?? 'application/octet-stream';
    }
}
