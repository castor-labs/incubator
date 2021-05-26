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

namespace Castor\Dapper;

/**
 * Class ErrorExecutor.
 */
final class EndOfStack implements Handler
{
    public function handle(object $command): void
    {
        if ($command instanceof Envelope) {
            $command = $command->unwrap();
        }

        throw new \RuntimeException(sprintf(
            'Could not handle command %s',
            get_class($command)
        ));
    }
}
