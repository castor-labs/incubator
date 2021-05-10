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

/**
 * Class LocalFileSupport.
 */
final class LocalFileSupport implements Middleware
{
    private Context $context;
    private string $path;

    /**
     * LocalFileSupport constructor.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function create(string $path = null): LocalFileSupport
    {
        return new self($path ?? getcwd().DIRECTORY_SEPARATOR.'static');
    }

    public function process(Context $ctx, Stack $stack): void
    {
        $ctx = new Extension\LocalFile($ctx, $this->path);
        $stack->next()->handle($ctx);
    }
}
