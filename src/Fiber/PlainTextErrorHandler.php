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

use Castor\Io\Error;
use Castor\Net\Http\ProtocolError;
use const Castor\Net\Http\STATUS_INTERNAL_SERVER_ERROR;
use Throwable;

/**
 * Class PlainTextErrorHandler handles errors and renders them in plain text.
 *
 * If trace is true, then the stack trace is also displayed.
 *
 * This error handler is intended to be used on development.
 */
final class PlainTextErrorHandler implements Middleware
{
    private bool $trace;

    /**
     * HandleError constructor.
     */
    public function __construct(bool $trace = true)
    {
        $this->trace = $trace;
    }

    /**
     * @throws Error
     */
    public function process(Context $ctx, Stack $stack): void
    {
        try {
            $stack->next()->handle($ctx);
        } catch (Throwable $e) {
            if (!$e instanceof ProtocolError) {
                $e = new ProtocolError(STATUS_INTERNAL_SERVER_ERROR, 'Internal Server Error', $e);
            }
            $text = $this->createErrorText($e);
            $ctx->text($text, $e->getCode());
        }
    }

    private function createErrorText(ProtocolError $error): string
    {
        $message = sprintf('HTTP ERROR %s: %s', $error->getCode(), $error->getMessage()).PHP_EOL;

        if (true === $this->trace) {
            $message .= PHP_EOL;
            $message .= $error->getTraceAsString();

            while (true) {
                $error = $error->getPrevious();
                if (!$error instanceof Throwable) {
                    break;
                }
                $message .= PHP_EOL.PHP_EOL;
                $message .= sprintf(
                    '%s thrown on %s, line %s',
                    get_class($error),
                    $error->getFile(),
                    $error->getLine()
                ).PHP_EOL;
                $message .= 'Error message: '.$error->getMessage().PHP_EOL;

                $message .= $error->getTraceAsString();
            }
        }

        return $message;
    }
}
