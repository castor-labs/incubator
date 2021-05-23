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

use PHPUnit\Framework\TestCase;

/**
 * Class BufferTest.
 *
 * @internal
 * @coversNothing
 */
class BufferTest extends TestCase
{
    public function testBufferOperations(): void
    {
        $buffer = Buffer::from('Hello');
        $buffer->seek(0, Seeker::START);
        $bytes = '';
        $buffer->read($bytes, 1);
        self::assertSame('H', $bytes);
        $buffer->readAt(3, $bytes, 2);
        self::assertSame('lo', $bytes);
        $buffer->seek(0, Seeker::END);
        $buffer->write(' World!');
        self::assertSame('Hello World!', (string) $buffer);
    }
}
