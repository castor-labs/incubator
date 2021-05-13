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

use Traversable;

/**
 * @psalm-return Traversable<Path>
 */
function glob(string $pattern, int $flags = 0): Traversable
{
    $arr = \glob($pattern, $flags);
    if (!is_array($arr)) {
        throw new \InvalidArgumentException('Invalid glob pattern provided');
    }
    foreach ($arr as $path) {
        yield Path::make($path);
    }
}
