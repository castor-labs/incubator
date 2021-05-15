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
use JsonException;

/**
 * Class Json represents a Json payload in a Request Body.
 */
final class Json implements Io\ReadCloser, Parser
{
    private Io\ReadCloser $reader;

    /**
     * Json constructor.
     */
    public function __construct(Io\ReadCloser $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @throws Io\Error
     */
    public function close(): void
    {
        $this->reader->close();
    }

    /**
     * @throws Eof
     * @throws Error
     */
    public function read(string &$bytes, int $length = Reader::DEFAULT_READ_SIZE): int
    {
        return $this->reader->read($bytes, $length);
    }

    public function getInnerBody(): Io\ReadCloser
    {
        return $this->reader;
    }

    /**
     * @throws Error
     * @throws JsonException
     */
    public function parse(): array
    {
        return json_decode(Io\readAll($this->reader), true, 512, JSON_THROW_ON_ERROR);
    }
}
