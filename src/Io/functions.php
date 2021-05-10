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

/**
 * Reads from a reader until Eof is reached and puts all the contents into
 * memory.
 *
 * @psalm-param positive-int $chunk
 *
 * @throws Error
 */
function readAll(Reader $reader, int $chunk = Reader::DEFAULT_READ_SIZE): string
{
    $contents = '';
    $bytes = '';
    while (true) {
        try {
            $reader->read($bytes, $chunk);
            $contents .= $bytes;
        } catch (Eof $e) {
            break;
        }
    }

    return $contents;
}

/**
 * Copies bytes from a reader to a writer.
 *
 * @psalm-param positive-int $chunk
 *
 * @throws Error
 *
 * @return int The amount of bytes copied
 */
function copy(Reader $reader, Writer $writer, int $chunk = 4096): int
{
    $copied = 0;
    $bytes = '';
    while (true) {
        try {
            $copied += $reader->read($bytes, $chunk);
            $writer->write($bytes);
        } catch (Eof $e) {
            break;
        }
    }

    return $copied;
}
