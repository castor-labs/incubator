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

namespace Castor\Fiber;

use Castor\Arr;

/**
 * Class MiddlewareHandler.
 */
final class HandlerMiddleware implements Handler, Stack
{
    private Middleware $middleware;
    private Handler $handler;

    /**
     * MiddlewareHandler constructor.
     */
    public function __construct(Middleware $middleware, Handler $handler)
    {
        $this->middleware = $middleware;
        $this->handler = $handler;
    }

    /**
     * @param Middleware ...$middleware
     */
    public static function stack(Handler $handler, Middleware ...$middleware): HandlerMiddleware
    {
        if ([] === $middleware) {
            throw new \RuntimeException('You need at least one middleware to create a stack');
        }
        $middleware = Arr\reverse($middleware);
        foreach ($middleware as $frame) {
            $handler = new self($frame, $handler);
        }

        return $handler;
    }

    public function handle(Context $ctx): void
    {
        $this->middleware->process($ctx, $this);
    }

    public function next(): Handler
    {
        return $this->handler;
    }
}
