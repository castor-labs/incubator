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

namespace Castor\Dapper\Handler;

/**
 * Interface Inflector.
 */
interface Inflector
{
    /**
     * Returns the handler name associated to a command.
     *
     * @throws InflectionError when the handler name cannot be figured out
     */
    public function inflect(object $command): string;
}
