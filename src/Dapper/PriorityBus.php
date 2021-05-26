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

namespace Castor\Dapper;

use Castor\Arr;
use SplPriorityQueue;

/**
 * Class PriorityBus.
 */
final class PriorityBus implements Handler
{
    public const LOWEST = 0;
    public const LOW = 50;
    public const MEDIUM = 100;
    public const HIGH = 150;
    public const HIGHEST = 200;

    /**
     * @var SplPriorityQueue<int,Middleware>
     */
    private SplPriorityQueue $middleware;

    /**
     * Bus constructor.
     */
    public function __construct()
    {
        $this->middleware = new SplPriorityQueue();
    }

    public function add(Middleware $middleware, int $priority = self::MEDIUM): void
    {
        $this->middleware->insert($middleware, $priority);
    }

    public function handle(object $command): void
    {
        $stack = MiddlewareStack::create(...Arr\fromIter($this->middleware));
        $stack->handle($command);
    }
}
