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
use Castor\Net\Http\Request;
use Castor\Net\Http\ResponseWriter;

/**
 * Class Router.
 *
 * This Router implements a middleware based execution model, very similar
 * to the Express JS framework.
 *
 * Routes are middleware that is matched in the order of registration, so you
 * have full control over which routes match first and how.
 *
 * This model is not very performant if you register all the routes in the same
 * router. Any routes that share a path should be put under a
 */
class Router implements Http\Handler, Handler
{
    private Config $config;
    /**
     * @var array|Middleware[]
     */
    private array $middleware;

    /**
     * Router constructor.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->middleware = [];
    }

    /**
     * Creates a Router with a default fallback handler.
     */
    public static function create(): Router
    {
        return new self(new Config());
    }

    /**
     * Register a middleware into the stack.
     */
    public function use(Middleware $middleware): Router
    {
        $this->middleware[] = $middleware;

        return $this;
    }

    /**
     * Registers a GET route.
     *
     * Get routes also match the HEAD verb
     */
    public function get(string $path, Handler $handler): Router
    {
        return $this->route(['GET', 'HEAD'], $path, $handler);
    }

    /**
     * Registers a both a GET and POST route.
     *
     * This is useful for html form as the GET does the rendering and the
     * POST does the processing of the form.
     */
    public function form(string $path, Handler $handler): Router
    {
        return $this->route(['GET', 'POST'], $path, $handler);
    }

    /**
     * Registers a POST route.
     */
    public function post(string $path, Handler $handler): Router
    {
        return $this->route(['POST'], $path, $handler);
    }

    /**
     * Registers a PUT route.
     */
    public function put(string $path, Handler $handler): Router
    {
        return $this->route(['PUT'], $path, $handler);
    }

    /**
     * Registers a PATCH route.
     */
    public function patch(string $path, Handler $handler): Router
    {
        return $this->route(['PATCH'], $path, $handler);
    }

    /**
     * Registers a DELETE route.
     */
    public function delete(string $path, Handler $handler): Router
    {
        return $this->route(['DELETE'], $path, $handler);
    }

    /**
     * Registers a OPTIONS route.
     */
    public function options(string $path, Handler $handler): Router
    {
        return $this->route(['OPTIONS'], $path, $handler);
    }

    /**
     * Registers a route.
     */
    public function route(array $methods, string $path, Handler $handler): Router
    {
        $this->use(Route::define($methods, $path, $handler));

        return $this;
    }

    /**
     * Mounts a handler into a path.
     *
     * If the path matches the handler will be executed.
     *
     * @return $this
     */
    public function mount(string $path, Handler $handler): Router
    {
        return $this->use(Path::define($path, $handler));
    }

    /**
     * Creates a group of routes under the specified path.
     *
     * This return a new router instance that is different from the current one.
     */
    public function group(string $path): Router
    {
        $router = new Router($this->config);
        $this->mount($path, $router);

        return $router;
    }

    /**
     * Handles a routing request.
     */
    public function handle(Context $ctx): void
    {
        $stack = HandlerMiddleware::stack(
            $this->config->fallback ?? new EndRouting(),
            ...$this->middleware
        );
        $stack->handle($ctx);
    }

    /**
     * @param ResponseWriter $writer
     * @param Request        $request
     */
    public function handleHTTP(Http\ResponseWriter $writer, Http\Request $request): void
    {
        $context = new DefaultContext($writer, $request, $this->config->engine);
        $this->handle($context);
    }
}
