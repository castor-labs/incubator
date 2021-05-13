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
use Castor\Mime\DefaultRegistry;

/**
 * Class File.
 */
class File implements Io\ReadSeeker, Io\WriteSeeker, Io\ReaderAt, Io\WriterAt, Io\WriterTo
{
    use Io\ResourceHelper;
    private Path $path;

    /**
     * File constructor.
     *
     * @param resource $resource
     *
     * @throws Io\Error
     */
    protected function __construct($resource, Path $path)
    {
        $this->setResource($resource);
        if (!stream_is_local($this->resource)) {
            throw new Io\Error('You must provide a local file');
        }
        $this->path = $path;
    }

    /**
     * @throws Io\Error
     */
    public static function open(string $path): File
    {
        $osPath = Path::make($path);
        if (!$osPath->isFile()) {
            throw new Io\Error('File does not exist');
        }
        $resource = fopen($osPath->toStr(), 'r+b');

        return new self($resource, $osPath);
    }

    /**
     * @throws Io\Error
     */
    public static function put(string $path): File
    {
        $osPath = Path::make($path);
        $resource = fopen($path, 'w+b');

        return new self($resource, $osPath);
    }

    /**
     * @throws Io\Error
     */
    public static function make(string $path): File
    {
        $osPath = Path::make($path);

        if ($osPath->isFile()) {
            throw new Io\Error('File already exists');
        }
        $resource = fopen($osPath->toStr(), 'x+b');

        return new self($resource, $osPath);
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

    public function getSize(): int
    {
        return filesize($this->path->toStr());
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function getContentType(): string
    {
        return DefaultRegistry::get()->getMimeType($this->path->getExtension()) ?? 'application/octet-stream';
    }
}
