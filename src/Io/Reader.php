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

/**
 * A Reader reads some bytes from a source.
 *
 * Implementations of `Reader` that compose another `Reader` MUST return the
 * actual number of bytes being returned to the caller if $bytes was modified.
 */
interface Reader
{
    public const DEFAULT_READ_SIZE = 4096;

    /**
     * Reads bytes from a source.
     *
     * This method will override the previous value of $bytes.
     *
     * Due to composed readers acting on the $bytes, `read` MAY
     * return less bytes than the actually requested.
     *
     * @param string $bytes  The read byte string
     * @param int    $length The amount of bytes to be read
     *
     * @throws Eof   when the end of file is reached
     * @throws Error when a reading error occurs
     *
     * @return int The actual number of bytes read
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int;
}
