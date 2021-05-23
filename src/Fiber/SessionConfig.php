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

use Brick\DateTime\Duration;
use Castor\Net\Http\Cookie;
use Closure;

/**
 * Class SessionConfig.
 */
class SessionConfig
{
    public Duration $ttl;
    public SessionStore $store;
    public Cookie $cookie;
    public Closure $idGenerator;

    /**
     * SessionConfig constructor.
     */
    public function __construct(Duration $ttl = null, SessionStore $store = null, Cookie $cookie = null, Closure $idGenerator = null)
    {
        $this->ttl = $ttl ?? Duration::ofHours(1);
        $this->store = $store ?? new OsSessionStore($this);
        $this->cookie = $cookie ?? Cookie::create('sess_id', '')->withPath('/');
        $this->idGenerator = $idGenerator ?? Closure::fromCallable(fn (): string => bin2hex(random_bytes(32)));
    }

    public function generateId(): string
    {
        return ($this->idGenerator)();
    }
}
