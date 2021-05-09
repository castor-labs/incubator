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

namespace Castor;

use PHPUnit\Framework\TestCase;

/**
 * Class ArrTest.
 *
 * @internal
 * @coversNothing
 */
class ArrTest extends TestCase
{
    public function testMap(): void
    {
        $arr = Arr::make(1, 2, 3, 4)->map(fn (int $num) => $num * 2);
        self::assertSame(2, $arr[0]);
        self::assertSame(4, $arr[1]);
        self::assertSame(6, $arr[2]);
        self::assertSame(8, $arr[3]);
    }
}
