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

use MNC\PathToRegExpPHP\NoMatchException;
use MNC\PathToRegExpPHP\PathRegExp;
use MNC\PathToRegExpPHP\PathRegExpFactory;

/**
 * Class Route.
 */
class Route implements Middleware
{
    private PathRegExp $regExp;
    private Handler $handler;
    private array $methods;

    /**
     * Route constructor.
     */
    protected function __construct(array $methods, PathRegExp $regExp, Handler $handler)
    {
        $this->methods = $methods;
        $this->regExp = $regExp;
        $this->handler = $handler;
    }

    public static function define(array $methods, string $path, Handler $handler): Route
    {
        return new self($methods, PathRegExpFactory::create($path), $handler);
    }

    public function process(Context $ctx, Stack $stack): void
    {
        $request = $ctx->getRequest();
        $context = $request->getContext();

        try {
            $path = $context->get('_router.path') ?? $request->getUri()->getPath();
            $result = $this->regExp->match((string) $path);
        } catch (NoMatchException $e) {
            $stack->next()->handle($ctx);

            return;
        }
        if (!$this->methodMatches($request->getMethod())) {
            $methods = $context->get(Context::ALLOWED_METHODS_ATTR) ?? [];
            $context->put(Context::ALLOWED_METHODS_ATTR, array_unique(array_merge($methods, $this->methods)));
            $stack->next()->handle($ctx);

            return;
        }
        // If both path and method matches, we store in context for next match
        $context->put(Context::PATH_ATTR, $path->replace($result->getMatchedString(), ''));
        $context->put(Context::PARAMS_ATTR, array_merge(
            $ctx->getParams(),
            $result->getValues()
        ));

        $this->handler->handle($ctx);
    }

    private function methodMatches(string $method): bool
    {
        return in_array($method, $this->methods, true);
    }
}
