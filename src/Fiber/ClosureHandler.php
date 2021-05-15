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

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use ReflectionType;
use RuntimeException;

/**
 * Class ClosureHandler.
 *
 * This class transforms callables into handlers.
 *
 * There are two transformation modes, a simple one that takes a callable with
 * the Castor\Fiber\Context as the only argument. This is very performant.
 *
 * The second mode uses reflection to analyze a callable and its arguments and
 * try to auto-inject them using several rules.
 */
final class ClosureHandler implements Handler
{
    private Closure $callable;
    private bool $reflect;
    private ?ContainerInterface $container;

    /**
     * HandlerFunc constructor.
     */
    public function __construct(Closure $callable, bool $reflect = false, ContainerInterface $container = null)
    {
        $this->callable = $callable;
        $this->reflect = $reflect;
        $this->container = $container;
    }

    /**
     * Makes an instance of Castor\Fiber\Handler out of a closure.
     *
     * The closure receives a Castor\Fiber\Context instance as the only argument.
     */
    public static function make(callable $callable): ClosureHandler
    {
        return new self(Closure::fromCallable($callable));
    }

    /**
     * Makes an instance of a Castor\Fiber\Handler out of a closure.
     *
     * The closure is reflected so parameters or context values can be injected
     * from the current request.
     *
     * If a ContainerInterface is provided, typed arguments are fetched from the
     * container and injected in the method call.
     */
    public static function reflect(callable $callable, ContainerInterface $container = null): ClosureHandler
    {
        return new self(Closure::fromCallable($callable), true, $container);
    }

    /**
     * @throws ReflectionException
     */
    public function handle(Context $ctx): void
    {
        if (false === $this->reflect) {
            ($this->callable)($ctx);

            return;
        }

        $rFunc = new ReflectionFunction($this->callable);
        $arguments = [];
        foreach ($rFunc->getParameters() as $param) {
            $arguments[] = $this->resolveParameter($param, $ctx);
        }
        ($this->callable)(...$arguments);
    }

    /**
     * @psalm-suppress UndefinedMethod
     * @throws ReflectionException
     *
     * @return mixed
     */
    private function resolveParameter(ReflectionParameter $param, Context $ctx)
    {
        $type = $param->getType();
        $name = $param->getName();
        $routeParams = $ctx->getParams();
        $request = $ctx->getRequest();
        $reqContext = $request->getContext();
        $writer = $ctx->getWriter();
        $isTyped = $type instanceof ReflectionType;

        if (!$isTyped) {
            // If the argument is not typed, then we can only resolve elements from
            // the context api by name, or any request parameters.
            if (array_key_exists($name, $routeParams)) {
                return $routeParams[$name] ?? null;
            }
            if ($reqContext->has($name)) {
                return $reqContext->get($name);
            }
            if ('ctx' === $name || 'context' === $name) {
                return $ctx;
            }
            if ('req' === $name || 'request' === $name) {
                return $request;
            }
            if ('w' === $name || 'writer' === $name) {
                return $writer;
            }
            if ($param->isOptional()) {
                return $param->getDefaultValue();
            }
            if ($param->allowsNull()) {
                return null;
            }

            throw new RuntimeException(sprintf(
                'Could not resolve argument %s ($%s). Try using type-hints for better reflection.',
                $param->getPosition(),
                $name,
            ));
        }

        $typeName = $type->getName();

        if ($type->isBuiltin()) {
            // If is a builtin type, we should try the request context or the route params.
            if (array_key_exists($name, $routeParams)) {
                return $routeParams[$name] ?? null;
            }
            if ($reqContext->has($name)) {
                return $reqContext->get($name);
            }
            if ($param->isOptional()) {
                return $param->getDefaultValue();
            }
            if ($param->allowsNull()) {
                return null;
            }

            throw new RuntimeException(sprintf(
                'Could not resolve argument %s ($%s) of type %s. Try making the argument optional.',
                $param->getPosition(),
                $name,
                $typeName
            ));
        }

        // We try to find the type in some of the request information.
        if ($ctx instanceof $typeName) {
            return $ctx;
        }
        if ($request instanceof $typeName) {
            return $request;
        }
        if ($writer instanceof $typeName) {
            return $writer;
        }
        // We try to find the type in the request context
        foreach ($reqContext->all() as $value) {
            if (is_object($value) && $value instanceof $typeName) {
                return $value;
            }
        }
        if (null !== $this->container && $this->container->has($typeName)) {
            return $this->container->get($typeName);
        }

        // We give it a last shot to optional arguments.
        if ($param->isOptional()) {
            return $param->getDefaultValue();
        }
        if ($param->allowsNull()) {
            return null;
        }

        throw new RuntimeException(sprintf(
            'Could not resolve argument %s ($%s) of type %s. Have you tried using a DI Container?',
            $param->getPosition(),
            $name,
            $typeName
        ));
    }
}
