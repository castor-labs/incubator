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
 * Class StaticPath.
 */
final class ServeStatic implements Handler
{
    private string $path;

    /**
     * ServeStatic constructor.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->guard();
    }

    public static function from(string $path): ServeStatic
    {
        return new self($path);
    }

    /**
     * @throws Error
     * @throws Http\ProtocolError
     */
    public function handle(Context $ctx): void
    {
        $request = $ctx->getRequest();
        $context = $request->getContext();
        $path = $context->get(Context::PATH_ATTR) ?? $request->getUri()->getPath();

        $filename = Os\Path\join($this->path, $path);

        try {
            $file = Os\File::open($filename);
        } catch (Os\Error $e) {
            throw new Http\ProtocolError(Http\STATUS_NOT_FOUND, sprintf('File %s does not exist', $path), $e);
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
