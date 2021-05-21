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

use Castor\Fiber\ClosureHandler;
use Castor\Fiber\Context;
use Castor\Fiber\PlainTextErrorHandler;
use Castor\Fiber\Router;
use Castor\Fiber\Session;
use Castor\Fiber\SessionSupport;

require_once dirname(__DIR__).'/vendor/autoload.php';

// You can run this example with php -S 0.0.0.0:8000 examples/routing.php

function hello(Context $ctx): void
{
    $ctx->json([
        'message' => 'Welcome to Fiber!',
    ]);
}

function greet(Context $ctx, string $name = null): void
{
    $name = $name ?? 'Person';
    $ctx->json([
        'message' => sprintf('Hello %s!', $name),
    ]);
}

function sessCount(Context $ctx, Session $sess): void
{
    $count = $sess->get('count') ?? 0;
    ++$count;
    $sess->set('count', $count);
    $ctx->json([
        'message' => sprintf('The count is %s', $count),
    ]);
}

$router = Router::create()
    ->use(new PlainTextErrorHandler())
    ->use(new SessionSupport())
    ->statics('/', __DIR__.'/static')
    ->get('/', ClosureHandler::make('hello'))
    ->get('/greet/:name?', ClosureHandler::reflect('greet'))
    ->get('/count', ClosureHandler::reflect('sessCount'))
;

Castor\Net\Http\Cgi\serve($router);
