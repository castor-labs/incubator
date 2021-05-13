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

/**
 * Class StaticPath.
 */
final class ServeStatic implements Handler
{
    private Os\Path $path;

    /**
     * ServeStatic constructor.
     */
    public function __construct(Os\Path $path)
    {
        $this->path = $path;
    }

    public static function from(string $path): ServeStatic
    {
        return new self(Os\Path::make($path));
    }

    /**
     * @throws Error
     * @throws Http\ProtocolError
     */
    public function handle(Context $ctx): void
    {
        $request = $ctx->getRequest();
        $context = $request->getContext();
        $path = $context->get('_router.path') ?? $request->getUri()->getPath();

        $filename = $this->path->join($path->toOsPath()->toStr());
        if (!$filename->isFile()) {
            throw new Http\ProtocolError(Http\STATUS_NOT_FOUND, sprintf('File %s does not exists', $path));
        }
        $file = Os\File::open($filename->toStr());
        $ctx->getWriter()->getHeaders()->add('Content-Type', $file->getContentType());
        $ctx->getWriter()->getHeaders()->add('Content-Length', (string) $file->getSize());
        $file->writeTo($ctx->getWriter());
    }
}
