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

namespace Castor\Net;

use Castor\Str;
use Stringable;

/**
 * Class Uri.
 */
class Uri implements Stringable
{
    private static array $defaultSchemePort = [
        'http' => '80',
        'https' => '443',
        'ftp' => '21',
        'ssh' => '22',
    ];

    private string $scheme;
    private string $user;
    private string $pass;
    private string $host;
    private string $port;
    private string $path;
    private string $query;
    private string $fragment;

    /**
     * Uri constructor.
     */
    protected function __construct(string $scheme, string $user = '', string $pass = '', string $host = '', string $port = '', string $path = '', string $query = '', string $fragment = '')
    {
        $this->scheme = $scheme;
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->port = $port;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    public function __toString(): string
    {
        return $this->toStr();
    }

    /**
     * @throws InvalidUri
     */
    public static function parse(string $uri): Uri
    {
        $parts = parse_url($uri);
        if (!is_array($parts)) {
            throw new InvalidUri(
                'Invalid URI specified at '.self::class.'::__construct Argument 1: '.$uri
            );
        }

        return new self(
            Str\toLower($parts['scheme'] ?? ''),
            $parts['user'] ?? '',
            $parts['pass'] ?? '',
            $parts['host'] ?? '',
            $parts['port'] ?? '',
            $parts['path'] ?? '',
            $parts['query'] ?? '',
            rawurlencode(rawurldecode($parts['fragment'] ?? '')),
        );
    }

    /**
     * @param string ...$parts
     */
    public static function join(Uri $uri, string ...$parts): Uri
    {
        $clone = clone $uri;
        $clone->path = Str\join($clone->path, ...$parts);

        return $clone;
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function withScheme(string $scheme): Uri
    {
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(bool $ignoreDefault = false): string
    {
        if (
            true === $ignoreDefault
            && $this->port === (self::$defaultSchemePort[$this->scheme] ?? '')
        ) {
            return '';
        }

        return $this->port;
    }

    public function getAuthority(): string
    {
        if ('' === $this->host) {
            return '';
        }
        $auth = '//';
        if ('' !== $this->user || '' !== $this->pass) {
            $auth .= $this->user.':'.$this->pass.'@';
        }
        $auth .= $this->host;
        $port = $this->getPort(true);
        if ('' !== $port) {
            $auth .= ':'.$port;
        }

        return $auth;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Returns the default port for the uri scheme.
     */
    public function getDefaultPort(): string
    {
        return static::$defaultSchemePort[$this->scheme] ?? '';
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function toStr(): string
    {
        $uri = $this->scheme.':';
        $uri .= $this->getAuthority();
        if ('' !== $this->path) {
            $uri .= $this->path;
        }
        if ('' !== $this->query) {
            $uri .= '?'.$this->query;
        }
        if ('' !== $this->fragment) {
            $uri .= '#'.$this->fragment;
        }

        return $uri;
    }

    /**
     * Test whether the specified string is a valid URI.
     */
    public static function isValid(string $uri): bool
    {
        try {
            self::parse($uri);
        } catch (InvalidUri $e) {
            return false;
        }

        return true;
    }
}
