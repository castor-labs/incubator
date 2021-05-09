<?php

declare(strict_types=1);

/**
 * @project Castor Io
 * @link https://github.com/castor-labs/io
 * @package castor/io
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Net\Uri;

use Castor\Net\Dns;
use Castor\Str;

/**
 * Class Authority.
 */
class Authority
{
    private const FLAG_IPV4 = 1;
    private const FLAG_IPV6 = 2;

    private string $userInfo;
    private string $host;
    private string $port;
    private int $flags;

    /**
     * Authority constructor.
     */
    protected function __construct(string $userInfo, string $host, string $port, int $flags)
    {
        $this->userInfo = $userInfo;
        $this->host = $host;
        $this->port = $port;
        $this->flags = $flags;
    }

    /**
     * @throws Dns\InvalidName
     */
    public static function create(string $host, string $port, string $userInfo): Authority
    {
        $flags = 0;
        // http://www.apps.ietf.org/rfc/rfc3986.html#sec-3.2.2
        // "Although host is case-insensitive, producers and normalizers should use lowercase for
        // registered names and hexadecimal addresses for the sake of uniformity"
        if ($inAddr = @\inet_pton(\trim($host, '[]'))) {
            $host = \strtolower($host);

            if (isset($inAddr[4])) {
                $flags += self::FLAG_IPV6;
            } else {
                $flags += self::FLAG_IPV4;
            }
        } elseif ($host) {
            $host = Dns\normalize($host);
        }

        return new self($userInfo, $host, $port, $flags);
    }

    public function getUserInfo(): Str
    {
        return Str::make($this->userInfo);
    }

    public function getHost(): Str
    {
        return Str::make($this->host);
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function isIpv4(): bool
    {
        return ($this->flags & self::FLAG_IPV4) !== 0;
    }

    public function isIpv6(): bool
    {
        return ($this->flags & self::FLAG_IPV6) !== 0;
    }
}
