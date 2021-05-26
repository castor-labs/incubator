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
 * An Envelope wraps commands for the command bus.
 *
 * This allows to decorate the original command with extra data the middlewares
 * in a command bus can use to behave differently.
 */
abstract class Envelope
{
    private object $command;

    /**
     * Envelope constructor.
     */
    public function __construct(object $command)
    {
        $this->command = $command;
    }

    public function open(string $instance): ?object
    {
        $command = $this;
        while ($command instanceof self) {
            if ($command instanceof $instance) {
                return $command;
            }
            $command = $command->getInnerCommand();
        }

        return null;
    }

    public function unwrap(): object
    {
        $command = $this;
        while ($command instanceof self) {
            $command = $command->getInnerCommand();
        }

        return $command;
    }

    /**
     * Returns the inner wrapped command.
     */
    public function getInnerCommand(): object
    {
        return $this->command;
    }
}
