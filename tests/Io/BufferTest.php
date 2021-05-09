<?php

namespace Castor\Io;

use PHPUnit\Framework\TestCase;

/**
 * Class BufferTest
 */
class BufferTest extends TestCase
{
    public function testBufferOperations(): void
    {
        $buffer = Buffer::from('Hello');
        $buffer->seek(0);
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
