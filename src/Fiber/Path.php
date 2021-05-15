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

use Castor\Net\Http;
use MNC\PathToRegExpPHP\NoMatchException;
use MNC\PathToRegExpPHP\PathRegExp;
use MNC\PathToRegExpPHP\PathRegExpFactory;

/**
 * Class Path.
 */
class Path implements Middleware
{
    private PathRegExp $regExp;
    private Handler $handler;

    /**
     * Route constructor.
     */
    protected function __construct(PathRegExp $regExp, Handler $handler)
    {
        $this->regExp = $regExp;
        $this->handler = $handler;
    }

    public static function define(string $path, Handler $handler): Path
    {
        return new self(PathRegExpFactory::create($path, 0), $handler);
    }

    /**
     * @throws Http\ProtocolError
     */
    public function process(Context $ctx, Stack $stack): void
    {
        $request = $ctx->getRequest();
        $context = $request->getContext();

        try {
            $path = $context->get(Context::PATH_ATTR) ?? $request->getUri()->getPath();
            $result = $this->regExp->match((string) $path);
        } catch (NoMatchException $e) {
            $stack->next()->handle($ctx);

            return;
        }

        $context->put(Context::PATH_ATTR, $path->replace($result->getMatchedString(), ''));
        $context->put(Context::PARAMS_ATTR, array_merge(
            $ctx->getParams(),
            $result->getValues()
        ));

        $this->handler->handle($ctx);
    }
}
