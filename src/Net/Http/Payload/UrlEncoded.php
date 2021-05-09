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

namespace Castor\Net\Http\Payload;

use Castor\Io;
use Castor\Io\Eof;
use Castor\Io\Error;
use Castor\Io\Reader;

/**
 * Class UrlEncoded represents a url encoded payload in a Request Body.
 */
final class UrlEncoded implements Io\ReadCloser
{
    private Io\ReadCloser $reader;
    private array $parsed;

    /**
     * Json constructor.
     */
    public function __construct(Io\ReadCloser $reader, array $parsed = [])
    {
        $this->reader = $reader;
        $this->parsed = $parsed;
    }

    /**
     * @throws Eof
     * @throws Error
     */
    public function read(string &$bytes, int $length = Reader::DEFAULT_READ_SIZE): int
    {
        return $this->reader->read($bytes, $length);
    }

    /**
     * @throws Io\Error
     */
    public function close(): void
    {
        $this->reader->close();
    }

    public function getInnerBody(): Io\ReadCloser
    {
        return $this->reader;
    }

    /**
     * @throws Io\Error
     */
    public function toArray(): array
    {
        if ([] === $this->parsed) {
            parse_str(Io\readAll($this->reader), $this->parsed);
        }

        return $this->parsed;
    }
}
