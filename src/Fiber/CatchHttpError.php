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

use Castor\Net\Http;

/**
 * Class CatchError.
 */
final class CatchHttpError implements Middleware
{
    private Middleware $middleware;
    private ?int $status;

    /**
     * CatchHttpError constructor.
     */
    public function __construct(Middleware $middleware, int $status = null)
    {
        $this->middleware = $middleware;
        $this->status = $status;
    }

    /**
     * @throws Http\ProtocolError
     */
    public function process(Context $ctx, Stack $stack): void
    {
        try {
            $this->middleware->process($ctx, $stack);
        } catch (Http\ProtocolError $e) {
            if (null === $this->status || $e->isCode($this->status)) {
                $stack->next()->handle($ctx);

                return;
            }

            throw $e;
        }
    }
}
