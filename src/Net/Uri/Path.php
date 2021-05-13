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

namespace Castor\Net\Uri;

use Castor\Str;

/**
 * Class Path.
 */
class Path extends Str
{
    /**
     * @param string ...$parts
     */
    public function merge(string ...$parts): Path
    {
        array_unshift($parts, $this->string);

        return self::make(implode('/', $parts));
    }

    public function toFsPath(): string
    {
        return $this->replace('/', DIRECTORY_SEPARATOR)->toStr();
    }
}
