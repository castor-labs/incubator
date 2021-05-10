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

namespace Castor\Mime;

/**
 * Class DefaultRegistry.
 */
class DefaultRegistry
{
    private static ?Registry $registry = null;

    public static function set(Registry $registry): void
    {
        if (null === self::$registry) {
            self::$registry = $registry;
        }
    }

    public static function get(): Registry
    {
        if (null === self::$registry) {
            self::$registry = LinuxRegistry::parse();
        }

        return self::$registry;
    }
}
