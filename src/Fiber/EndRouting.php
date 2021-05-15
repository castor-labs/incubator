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

use Castor\Net\Http\ProtocolError;
use const Castor\Net\Http\STATUS_METHOD_NOT_ALLOWED;
use const Castor\Net\Http\STATUS_NOT_FOUND;

/**
 * The EndRouting handler throws a 404 or 405 Http exceptions.
 */
final class EndRouting implements Handler
{
    /**
     * @throws ProtocolError
     */
    public function handle(Context $ctx): void
    {
        $allowedMethods = $ctx->getRequest()->getContext()->get(Context::ALLOWED_METHODS_ATTR) ?? [];
        if ([] === $allowedMethods) {
            throw new ProtocolError(
                STATUS_NOT_FOUND,
                sprintf(
                    'Could not %s %s',
                    $ctx->getRequest()->getMethod(),
                    $ctx->getRequest()->getUri()->getPath()
                )
            );
        }

        throw new ProtocolError(
            STATUS_METHOD_NOT_ALLOWED,
            sprintf(
                'Could not match %s %s. Allowed methods: %s',
                $ctx->getRequest()->getMethod(),
                $ctx->getRequest()->getUri()->getPath(),
                implode(', ', $allowedMethods)
            )
        );
    }
}
