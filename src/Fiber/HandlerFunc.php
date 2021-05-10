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

use Psr\Container\ContainerInterface;

/**
 * Class HandlerFunc.
 */
final class HandlerFunc implements Handler
{
    /**
     * @var callable
     */
    private $callable;
    private ?ContainerInterface $container;
    private bool $reflect;

    /**
     * HandlerFunc constructor.
     */
    public function __construct(callable $callable, bool $reflect = false, ContainerInterface $container = null)
    {
        $this->callable = $callable;
        $this->reflect = $reflect;
        $this->container = $container;
    }

    public static function make(callable $callable): HandlerFunc
    {
        return new self($callable);
    }

    public static function reflect(callable $callable, ContainerInterface $container = null): HandlerFunc
    {
        return new self($callable, true, $container);
    }

    public function handle(Context $ctx): void
    {
        if (false === $this->reflect) {
            ($this->callable)($ctx);

            return;
        }

        // TODO: We attempt to inject services and parameters to this handler.
    }
}
