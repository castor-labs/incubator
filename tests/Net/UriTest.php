<?php

declare(strict_types=1);

/**
 * @project Castor Io
 * @link https://github.com/castor-labs/io
 * @package castor/io
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Net;

use PHPUnit\Framework\TestCase;

/**
 * Class UriTest.
 *
 * @internal
 * @coversNothing
 */
class UriTest extends TestCase
{
    public function testItParsesEveryComponent(): void
    {
        $uri = Uri::parse('https://mnavarro.dev/home?foo=bar&bar=foo#something');
        self::assertTrue($uri->getScheme()->equals('https'));
        self::assertSame('443', $uri->getScheme()->getDefaultPort());
        self::assertTrue($uri->getScheme()->isSecure());
        self::assertTrue($uri->getAuthority()->getHost()->equals('mnavarro.dev'));
        self::assertSame('', $uri->getAuthority()->getPort());
        self::assertTrue($uri->getFragment()->equals('something'));
        self::assertSame('foo=bar&bar=foo', (string) $uri->getQuery());
        self::assertTrue($uri->getQuery()->has('foo'));
        self::assertTrue($uri->getQuery()->has('bar'));
        self::assertTrue($uri->getPath()->equals('/home'));
        self::assertSame('foo', $uri->getQuery()->get('bar')[0]);
        self::assertSame('bar', $uri->getQuery()->get('foo')[0]);
    }
}
