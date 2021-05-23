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

namespace Castor\Io;

use Psr\Http\Message\StreamInterface;
use RuntimeException;

/**
 * Class ReaderToPsrStream.
 *
 * This stream implementation adapts the different Castor\Io interfaces to a
 * PSR-7 StreamInterface.
 */
final class ReaderToPsrStream implements StreamInterface
{
    private Reader $reader;
    private bool $eof = false;
    private bool $closed = false;
    private bool $detached = false;

    /**
     * Psr7StreamReader constructor.
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function __toString(): string
    {
        return $this->getContents();
    }

    public function close(): void
    {
        if ($this->reader instanceof Closer) {
            try {
                $this->reader->close();
            } catch (Error $e) {
                throw new RuntimeException('There was an error while closing the underlying reader', 0, $e);
            }
        }
        $this->closed = true;
    }

    public function detach()
    {
        $this->closed = true;
    }

    public function getSize(): ?int
    {
        if ($this->reader instanceof Sizer) {
            return $this->reader->size();
        }

        return null;
    }

    public function tell(): int
    {
        return $this->innerSeek(0, Seeker::CURRENT);
    }

    public function eof(): bool
    {
        return $this->eof;
    }

    public function isSeekable(): bool
    {
        return $this->reader instanceof Seeker;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->innerSeek($offset, $whence);
    }

    public function rewind(): void
    {
        $this->innerSeek(0, Seeker::START);
    }

    public function isWritable(): bool
    {
        return $this->reader instanceof Writer;
    }

    public function write($string): int
    {
        if (!$this->reader instanceof Writer) {
            throw new RuntimeException('The underlying reader is not writable');
        }

        try {
            return $this->reader->write($string);
        } catch (Error $e) {
            throw new RuntimeException('There was an error while writing to the the underlying reader', 0, $e);
        }
    }

    public function isReadable(): bool
    {
        return true;
    }

    /**
     * @param int $length
     */
    public function read($length): string
    {
        $this->ensureCanOperate();
        if (true === $this->eof) {
            return '';
        }
        $bytes = '';

        try {
            $this->reader->read($bytes, $length);
        } catch (Eof $e) {
            $this->eof = true;
        } catch (Error $e) {
            throw new RuntimeException('There was an error while reading from the underlying reader', 0, $e);
        }

        return $bytes;
    }

    public function getContents(): string
    {
        if ($this->reader instanceof Seeker) {
            try {
                $this->reader->seek(0, Seeker::START);
            } catch (Error $e) {
                throw new RuntimeException('Error while seeking the underlying reader', 0, $e);
            }
        }

        try {
            return readAll($this->reader);
        } catch (Error $e) {
            throw new RuntimeException('Error reading from the underlying reader', 0, $e);
        }
    }

    public function getMetadata($key = null): array
    {
        return [];
    }

    private function innerSeek(int $offset, int $whence): int
    {
        $this->ensureCanOperate();
        if (!$this->reader instanceof Seeker) {
            throw new RuntimeException('The underlying reader is not seekable');
        }

        try {
            return $this->reader->seek($offset, $whence);
        } catch (Error $e) {
            throw new RuntimeException('There was an error while seeking in the underlying reader', 0, $e);
        }
    }

    private function ensureCanOperate(): void
    {
        if (true === $this->detached) {
            throw new RuntimeException('The stream is detached');
        }
        if (true === $this->closed) {
            throw new RuntimeException('The stream is closed');
        }
    }
}
