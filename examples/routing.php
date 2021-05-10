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

use Castor\Fiber\Context;
use Castor\Fiber\HandlerFunc;
use Castor\Fiber\LocalFileSupport;
use Castor\Fiber\PlainTextErrorHandler;
use Castor\Fiber\Router;
use Castor\Fiber\TemplateSupport;
use Castor\Template\PhpEngine;

require_once dirname(__DIR__).'/vendor/autoload.php';

// You can run this example with php -S 0.0.0.0:8000 examples/routing.php

function hello(Context $ctx): void
{
    $ctx->json([
        'message' => 'Welcome to Fiber!',
    ]);
}

$router = Router::create()
    ->use(new PlainTextErrorHandler())
    ->get('/', HandlerFunc::make('hello'))
;

Castor\Net\Http\Cgi\serve($router);
