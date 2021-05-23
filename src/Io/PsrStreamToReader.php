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

use Castor\Str;
use Psr\Http\Message\StreamInterface;

/**
 * Class PsrStreamToReader adapts a StreamInterface into a Castor\Io\Reader.
 */
final class PsrStreamToReader implements Reader
{
    private StreamInterface $stream;

    /**
     * PsrStreamToReader constructor.
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        if ($this->stream->eof()) {
            throw new Eof('End of file reached in the underlying stream');
        }
        if (!$this->stream->isReadable()) {
            throw new Error('The underlying stream is not readable');
        }
        $bytes = $this->stream->read($length);

        return Str\length($bytes);
    }
}
