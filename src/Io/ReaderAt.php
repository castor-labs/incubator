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
 * A ReaderAt allows reading a slice of bytes at an offset.
 */
interface ReaderAt
{
    /**
     * @throws Error
     */
    public function readAt(int $offset, string &$bytes, int $length = Reader::DEFAULT_READ_SIZE): int;
}
