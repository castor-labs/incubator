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

use Castor\Fs\File;
use Castor\Io\Error;

/**
 * Class StaticPath.
 */
final class ServeStatic implements Middleware
{
    private string $path;

    /**
     * ServeStatic constructor.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function from(string $path): ServeStatic
    {
        return new self($path);
    }

    /**
     * @throws Error
     */
    public function process(Context $ctx, Stack $stack): void
    {
        $filename = $this->path.$ctx->getRequest()->getUri()->getPath();
        if (!File::exists($filename)) {
            $stack->next()->handle($ctx);

            return;
        }
        $file = File::open($filename);
        $ctx->getWriter()->getHeaders()->add('Content-Type', $file->getContentType());
        $ctx->getWriter()->getHeaders()->add('Content-Length', (string) $file->getSize());
        $file->writeTo($ctx->getWriter());
    }
}
