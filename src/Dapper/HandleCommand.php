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
 * Class HandleCommand.
 */
final class HandleCommand implements Middleware
{
    private Handler\Inflector $inflector;
    private Handler\Locator $locator;

    /**
     * HandleCommand constructor.
     */
    public function __construct(Handler\Inflector $inflector, Handler\Locator $locator)
    {
        $this->inflector = $inflector;
        $this->locator = $locator;
    }

    /**
     * @throws Handler\HandlerNotFound
     * @throws Handler\InflectionError
     */
    public function process(object $command, Stack $stack): void
    {
        if ($command instanceof Envelope) {
            $command = $command->unwrap();
        }

        $handlerName = $this->inflector->inflect($command);
        $handler = $this->locator->locate($handlerName);
        $handler->handle($command);
    }
}
