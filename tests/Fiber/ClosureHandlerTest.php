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

namespace Castor\Fiber;

use Castor\Net\Http;
use PHPUnit\Framework\TestCase;

/**
 * Class CallableHandlerTest.
 *
 * @internal
 * @coversNothing
 */
class ClosureHandlerTest extends TestCase
{
    public function testItReflectsNonTypedArguments(): void
    {
        $callable = static function ($ctx, $req, $writer, $id) {
            self::assertInstanceOf(Context::class, $ctx);
            self::assertInstanceOf(Http\Request::class, $req);
            self::assertInstanceOf(Http\ResponseWriter::class, $writer);
            self::assertSame('some-id', $id);
        };

        $handler = ClosureHandler::reflect($callable);
        $ctxMock = $this->createMock(Context::class);
        $reqMock = $this->createMock(Http\Request::class);
        $reqCtxMock = $this->createMock(Http\Context::class);
        $writerStub = $this->createMock(Http\ResponseWriter::class);

        $ctxMock->expects(self::atLeastOnce())
            ->method('getParams')
            ->willReturn(['id' => 'some-id'])
        ;
        $ctxMock->expects(self::atLeastOnce())
            ->method('getRequest')
            ->willReturn($reqMock)
        ;
        $ctxMock->expects(self::atLeastOnce())
            ->method('getWriter')
            ->willReturn($writerStub)
        ;
        $reqMock->expects(self::atLeastOnce())
            ->method('getContext')
            ->willReturn($reqCtxMock)
        ;
        $reqCtxMock->expects(self::exactly(3))
            ->method('has')
            ->withConsecutive(['ctx'], ['req'], ['writer'])
            ->willReturnOnConsecutiveCalls(false, false, false)
        ;

        $handler->handle($ctxMock);
    }

    public function testItThrowsExceptionOnUnresolvableUntypedArgument(): void
    {
        self::markTestSkipped('Possible bug in reflection parameters');
        $callable = static function ($unknown) {};

        $handler = ClosureHandler::reflect($callable);
        $ctxMock = $this->createMock(Context::class);
        $reqMock = $this->createMock(Http\Request::class);
        $reqCtxMock = $this->createMock(Http\Context::class);
        $writerStub = $this->createMock(Http\ResponseWriter::class);

        $ctxMock->expects(self::atLeastOnce())
            ->method('getParams')
            ->willReturn(['id' => 'some-id'])
        ;
        $ctxMock->expects(self::atLeastOnce())
            ->method('getRequest')
            ->willReturn($reqMock)
        ;
        $ctxMock->expects(self::atLeastOnce())
            ->method('getWriter')
            ->willReturn($writerStub)
        ;
        $reqMock->expects(self::atLeastOnce())
            ->method('getContext')
            ->willReturn($reqCtxMock)
        ;
        $reqCtxMock->expects(self::once())
            ->method('has')
            ->with('unknown')
            ->willReturnOnConsecutiveCalls(false)
        ;
        $this->expectException(\RuntimeException::class);
        $handler->handle($ctxMock);
    }

    public function testItResolvesTypedArguments(): void
    {
        $callable = static function (Context $ctx, string $id) {
            self::assertInstanceOf(Context::class, $ctx);
            self::assertSame('some-id', $id);
        };

        $handler = ClosureHandler::reflect($callable);
        $ctxMock = $this->createMock(Context::class);
        $reqMock = $this->createMock(Http\Request::class);
        $reqCtxMock = $this->createMock(Http\Context::class);
        $writerStub = $this->createMock(Http\ResponseWriter::class);

        $ctxMock->expects(self::atLeastOnce())
            ->method('getParams')
            ->willReturn(['id' => 'some-id'])
        ;
        $ctxMock->expects(self::atLeastOnce())
            ->method('getRequest')
            ->willReturn($reqMock)
        ;
        $ctxMock->expects(self::atLeastOnce())
            ->method('getWriter')
            ->willReturn($writerStub)
        ;
        $reqMock->expects(self::atLeastOnce())
            ->method('getContext')
            ->willReturn($reqCtxMock)
        ;

        $handler->handle($ctxMock);
    }
}
