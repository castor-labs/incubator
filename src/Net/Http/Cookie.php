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

namespace Castor\Net\Http;

use Brick\DateTime\Instant;
use Brick\DateTime\TimeZoneRegion;

/**
 * Class Cookie represents a cookie that will be set in a HTTP response.
 */
class Cookie
{
    private string $name;
    private string $value;
    private string $path;
    private string $domain;
    private ?Instant $expires;
    private ?SameSite $sameSite;
    private ?int $maxAge;
    private bool $secure;
    private bool $httpOnly;

    /**
     * Cookie constructor.
     */
    public function __construct(
        string $name,
        string $value,
        string $path = '',
        string $domain = '',
        Instant $expires = null,
        SameSite $sameSite = null,
        int $maxAge = null,
        bool $secure = false,
        bool $httpOnly = false
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->path = $path;
        $this->domain = $domain;
        $this->expires = $expires;
        $this->sameSite = $sameSite;
        $this->maxAge = $maxAge;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    public static function create(string $name, string $value): Cookie
    {
        return new self($name, $value);
    }

    public function secure(): Cookie
    {
        $clone = clone $this;
        $clone->secure = true;

        return $clone;
    }

    public function httpOnly(): Cookie
    {
        $clone = clone $this;
        $clone->httpOnly = true;

        return $clone;
    }

    public function withDomain(string $domain): Cookie
    {
        $clone = clone $this;
        $clone->domain = $domain;

        return $clone;
    }

    public function withPath(string $path): Cookie
    {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    public function withSameSite(SameSite $sameSite): Cookie
    {
        $clone = clone $this;
        $clone->sameSite = $sameSite;

        return $clone;
    }

    public function withMaxAge(int $maxAge): Cookie
    {
        $clone = clone $this;
        $clone->maxAge = $maxAge;

        return $clone;
    }

    public function withExpires(Instant $expires): Cookie
    {
        $clone = clone $this;
        $clone->expires = $expires;

        return $clone;
    }

    public function toHttpString(): string
    {
        $str = $this->name.'='.$this->value;
        if ($this->expires instanceof Instant) {
            $time = $this->expires
                ->atTimeZone(TimeZoneRegion::parse('GMT'))
                ->toDateTimeImmutable()
                ->format(\DateTime::COOKIE)
            ;
            $str .= '; Expires='.$time;
        }
        if ('' !== $this->path) {
            $str .= '; Path='.$this->path;
        }
        if ('' !== $this->domain) {
            $str .= '; Domain='.$this->domain;
        }
        if (null !== $this->maxAge) {
            $str .= '; Max-Age='.$this->maxAge;
        }
        if ($this->secure) {
            $str .= '; Secure';
        }
        if ($this->httpOnly) {
            $str .= '; HttpOnly';
        }
        if ($this->sameSite instanceof SameSite) {
            $str .= '; SameSite='.$this->sameSite->toStr();
        }

        return $str;
    }
}
