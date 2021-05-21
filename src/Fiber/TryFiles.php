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
use Castor\Net\Http;
use Castor\Os;
use InvalidArgumentException;

/**
 * Class TryFiles.
 */
final class TryFiles implements Middleware
{
    private string $path;
    private bool $throwNotFound;

    /**
     * ServeStatic constructor.
     */
    public function __construct(string $path, bool $throwNotFound = true)
    {
        $this->path = $path;
        $this->throwNotFound = $throwNotFound;
        $this->guard();
    }

    /**
     * @throws Error
     * @throws Http\ProtocolError
     */
    public function process(Context $ctx, Stack $stack): void
    {
        $request = $ctx->getRequest();
        $context = $request->getContext();
        $path = $context->get(Context::PATH_ATTR) ?? $request->getUri()->getPath();

        $filename = Os\Path\join($this->path, $path);

        try {
            $file = Os\File::open($filename);
        } catch (Os\Error $e) {
            if ($this->throwNotFound && '' !== Os\Path\extension($filename)) {
                throw new Http\ProtocolError(Http\STATUS_NOT_FOUND);
            }
            $stack->next()->handle($ctx);

            return;
        }
        $ctx->getWriter()->getHeaders()->add('Content-Type', $file->getMimeType());
        $ctx->getWriter()->getHeaders()->add('Content-Length', (string) $file->size());
        $file->writeTo($ctx->getWriter());
    }

    private function guard(): void
    {
        if (!Os\Path\isDirectory($this->path)) {
            throw new InvalidArgumentException('Argument 1 passed to %s::__construct must be a valid filesystem directory', __CLASS__);
        }
    }
}
