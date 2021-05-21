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
        self::assertSame('https', $uri->getScheme());
        self::assertSame('443', $uri->getDefaultPort());
        self::assertSame('mnavarro.dev', $uri->getHost());
        self::assertSame('', $uri->getPort());
        self::assertSame('/home', $uri->getPath());
        self::assertSame('foo=bar&bar=foo', $uri->getQuery());
        self::assertSame('something', $uri->getFragment());
        self::assertSame('https://mnavarro.dev/home?foo=bar&bar=foo#something', (string) $uri);
    }
}
