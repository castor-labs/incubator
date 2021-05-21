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
 * Class StartSession.
 */
final class SessionSupport implements Middleware
{
    private SessionConfig $config;

    /**
     * StartSession constructor.
     */
    public function __construct(SessionConfig $config = null)
    {
        $this->config = $config ?? new SessionConfig();
    }

    /**
     * @throws Http\ProtocolError
     */
    public function process(Context $ctx, Stack $stack): void
    {
        $session = $this->config->store->get($ctx);
        $ctx = StatefulContext::initialize($ctx, $session);
        $stack->next()->handle($ctx);
        $session->save();
    }
}
