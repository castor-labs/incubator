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

namespace Castor\Net;

/**
 * Class Uri.
 */
class Uri
{
    private Uri\Scheme $scheme;
    private Uri\Authority $authority;
    private Uri\Path $path;
    private Uri\Query $query;
    private Uri\Fragment $fragment;

    /**
     * Uri constructor.
     */
    protected function __construct(Uri\Scheme $scheme, Uri\Authority $authority, Uri\Path $path, Uri\Query $query, Uri\Fragment $fragment)
    {
        $this->scheme = $scheme;
        $this->authority = $authority;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;
    }

    public function __clone()
    {
        $this->query = clone $this->query;
        $this->authority = clone $this->authority;
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

        // http://www.apps.ietf.org/rfc/rfc3986.html#sec-3.1
        // "schemes are case-insensitive"
        $scheme = new Uri\Scheme($parts['scheme'] ?? '');
        $host = $parts['host'] ?? '';
        $port = $parts['port'] ?? '';
        $path = $parts['path'] ?? '';
        $user = $parts['user'] ?? '';
        $pass = $parts['pass'] ?? '';
        $fragment = $parts['fragment'] ?? '';
        $rawQuery = $parts['query'] ?? '';

        $userInfo = '';
        if ('' !== $user) {
            $userInfo .= $user;
        }
        if ('' !== $pass) {
            $userInfo .= ':'.$pass;
        }

        $fragment = rawurlencode(rawurldecode($fragment));

        try {
            return new self(
                $scheme,
                Uri\Authority::create($host, $port, $userInfo),
                Uri\Path::make($path),
                Uri\Query::parse($rawQuery),
                Uri\Fragment::make($fragment)
            );
        } catch (Dns\InvalidName $e) {
            throw new InvalidUri("Invalid URI: Invalid host: {$host}", 0, $e);
        }
    }

    /**
     * @param string ...$parts
     */
    public static function join(Uri $uri, string ...$parts): Uri
    {
        $clone = clone $uri;
        $clone->path = $clone->path->merge(...$parts);

        return $clone;
    }

    public function getScheme(): Uri\Scheme
    {
        return $this->scheme;
    }

    public function withScheme(Uri\Scheme $scheme): Uri
    {
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    public function getAuthority(): Uri\Authority
    {
        return $this->authority;
    }

    public function getPath(): Uri\Path
    {
        return $this->path;
    }

    public function getQuery(): Uri\Query
    {
        return $this->query;
    }

    public function getFragment(): Uri\Fragment
    {
        return $this->fragment;
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
