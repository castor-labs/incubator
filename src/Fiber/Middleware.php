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

/**
 * Interface Handler.
 */
interface Middleware
{
    /**
     * Process the Fiber context.
     *
     * The middleware has the option to delegate to the next item in the stack.
     *
     * @throws Http\ProtocolError when an error occurs
     */
    public function process(Context $ctx, Stack $stack): void;
}
