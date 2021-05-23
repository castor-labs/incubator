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

namespace Castor\Storage;

use Castor\Io\Reader;
use Castor\Net\Http\Request;

/**
 * Class ReaderBlob.
 */
final class ReaderBlob implements Blob
{
    private Reader $reader;
    private string $contentType;
    private int $size;

    /**
     * ReaderBlob constructor.
     */
    public function __construct(Reader $reader, string $contentType, int $size)
    {
        $this->reader = $reader;
        $this->contentType = $contentType;
        $this->size = $size;
    }

    public static function fromRequest(Request $request): ReaderBlob
    {
        return new self(
            $request->getBody(),
            $request->getHeaders()->get('Content-Type')[0] ?? 'application/octet-stream',
            (int) ($request->getHeaders()->get('Content-Length')[0] ?? '0')
        );
    }

    public function getMimeType(): string
    {
        return $this->contentType;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function getReader(): Reader
    {
        return $this->reader;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->reader->read($bytes, $length);
    }
}
