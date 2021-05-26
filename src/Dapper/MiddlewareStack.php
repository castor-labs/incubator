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

use Castor\Arr;
use InvalidArgumentException;

/**
 * Class MiddlewareStack.
 */
final class MiddlewareStack implements Stack, Handler
{
    private Middleware $middleware;
    private Handler $handler;

    /**
     * MiddlewareStack constructor.
     */
    public function __construct(Middleware $middleware, Handler $handler)
    {
        $this->middleware = $middleware;
        $this->handler = $handler;
    }

    /**
     * @param Middleware ...$middleware
     */
    public static function create(Middleware ...$middleware): MiddlewareStack
    {
        $middleware = Arr\reverse($middleware);
        $executor = new EndOfStack();
        foreach ($middleware as $frame) {
            $executor = new self($frame, $executor);
        }
        if (!$executor instanceof self) {
            throw new InvalidArgumentException('You need at least one middleware to create a stack');
        }

        return $executor;
    }

    public function handle(object $command): void
    {
        $this->middleware->process($command, $this);
    }

    public function next(): Handler
    {
        return $this->handler;
    }
}
