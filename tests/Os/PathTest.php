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

namespace Castor\Os;

use PHPUnit\Framework\TestCase;

/**
 * Class PathTest.
 *
 * @internal
 * @coversNothing
 */
class PathTest extends TestCase
{
    /**
     * @dataProvider getPathsForBoolean
     */
    public function testItCheckBooleanMethods(string $path, bool $exists, bool $abs, bool $file, bool $dir): void
    {
        $path = Path::make($path);
        self::assertSame($exists, $path->exists());
        self::assertSame($abs, $path->isAbsolute());
        self::assertSame($file, $path->isFile());
        self::assertSame($dir, $path->isDirectory());
    }

    public function getPathsForBoolean(): array
    {
        return [
            ['/', true, true, false, true],
            ['/dev', true, true, false, true],
            ['src', true, false, false, true],
            ['src/Str.php', true, false, true, false],
            ['c:\\\\some\\win\\file.png', false, true, false, false],
        ];
    }
}
