<?php

declare(strict_types=1);

/**
 * @project Castor Incubator
 * @link https://github.com/castor-labs/incubator
 * @package castor/incubator
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Fiber;

use Castor\Arr;
use Castor\Str;
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

    /**
     * {@inheritDoc}
     */
    public function process(Context $ctx, Stack $stack): void
    {
        $request = $ctx->getRequest();
        $reqCtx = $request->getContext();

        try {
            $path = $reqCtx->get(Context::PATH_ATTR) ?? $request->getUri()->getPath();
            $result = $this->regExp->match($path);
        } catch (NoMatchException $e) {
            $stack->next()->handle($ctx);

            return;
        }
        if (!$this->methodMatches($request->getMethod())) {
            $methods = $reqCtx->get(Context::ALLOWED_METHODS_ATTR) ?? [];
            $reqCtx->put(Context::ALLOWED_METHODS_ATTR, Arr\unique(Arr\merge($methods, $this->methods)));
            $stack->next()->handle($ctx);

            return;
        }
        // If both path and method matches, we store in context for next match
        $reqCtx->put(Context::PATH_ATTR, Str\replace($path, $result->getMatchedString(), ''));
        $reqCtx->put(Context::PARAMS_ATTR, array_merge(
            $ctx->getParams(),
            $result->getValues()
        ));

        $this->handler->handle($ctx);
    }

    private function methodMatches(string $method): bool
    {
        return Arr\has($this->methods, $method);
    }
}
