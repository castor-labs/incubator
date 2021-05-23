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

namespace Castor\Net\Http\Cgi;

use Castor\Net;
use Castor\Str;

/**
 * Class Uri.
 */
final class Uri extends Net\Uri
{
    public static function marshalFromSapi(array $server, array $headers): Uri
    {
        /**
         * Retrieve a header value from an array of headers using a case-insensitive lookup.
         *
         * @param array $headers Key/value header pairs
         * @param mixed $default Default value to return if header not found
         *
         * @return mixed
         */
        $getHeaderFromArray = static function (string $name, array $headers, $default = null) {
            $header = strtolower($name);
            $headers = array_change_key_case($headers, CASE_LOWER);
            if (array_key_exists($header, $headers)) {
                return is_array($headers[$header]) ? implode(', ', $headers[$header]) : $headers[$header];
            }

            return $default;
        };

        /**
         * Marshal the host and port from HTTP headers and/or the PHP environment.
         *
         * @return array array of two items, host and port, in that order (can be
         *               passed to a list() operation)
         */
        $marshalHostAndPort = static function (array $headers, array $server) use ($getHeaderFromArray): array {
            /**
             * @param array|string $host
             *
             * @return array array of two items, host and port, in that order (can be
             *               passed to a list() operation)
             */
            $marshalHostAndPortFromHeader = static function ($host) {
                if (is_array($host)) {
                    $host = implode(', ', $host);
                }

                $port = null;

                // works for regname, IPv4 & IPv6
                if (preg_match('|\:(\d+)$|', $host, $matches)) {
                    $host = substr($host, 0, -1 * (strlen($matches[1]) + 1));
                    $port = (int) $matches[1];
                }

                return [$host, $port];
            };

            /**
             * @return array array of two items, host and port, in that order (can be
             *               passed to a list() operation)
             */
            $marshalIpv6HostAndPort = static function (array $server, ?int $port): array {
                $host = '['.$server['SERVER_ADDR'].']';
                $port = $port ?: 80;
                if ($port.']' === substr($host, strrpos($host, ':') + 1)) {
                    // The last digit of the IPv6-Address has been taken as port
                    // Unset the port so the default port can be used
                    $port = null;
                }

                return [$host, $port];
            };

            static $defaults = ['', null];

            $forwardedHost = $getHeaderFromArray('x-forwarded-host', $headers, false);
            if (false !== $forwardedHost) {
                return $marshalHostAndPortFromHeader($forwardedHost);
            }

            $host = $getHeaderFromArray('host', $headers, false);
            if (false !== $host) {
                return $marshalHostAndPortFromHeader($host);
            }

            if (!isset($server['SERVER_NAME'])) {
                return $defaults;
            }

            $host = $server['SERVER_NAME'];
            $port = isset($server['SERVER_PORT']) ? (int) $server['SERVER_PORT'] : null;

            if (!isset($server['SERVER_ADDR'])
                || !preg_match('/^\[[0-9a-fA-F\:]+\]$/', $host)
            ) {
                return [$host, $port];
            }

            // Misinterpreted IPv6-Address
            // Reported for Safari on Windows
            return $marshalIpv6HostAndPort($server, $port);
        };

        /**
         * Detect the path for the request.
         *
         * Looks at a variety of criteria in order to attempt to autodetect the base
         * request path, including:
         *
         * - IIS7 UrlRewrite environment
         * - REQUEST_URI
         * - ORIG_PATH_INFO
         *
         * From Laminas\Http\PhpEnvironment\Request class
         */
        $marshalRequestPath = static function (array $server): string {
            // IIS7 with URL Rewrite: make sure we get the unencoded url
            // (double slash problem).
            $iisUrlRewritten = $server['IIS_WasUrlRewritten'] ?? null;
            $unencodedUrl = $server['UNENCODED_URL'] ?? '';
            if ('1' === $iisUrlRewritten && !empty($unencodedUrl)) {
                return $unencodedUrl;
            }

            $requestUri = $server['REQUEST_URI'] ?? null;

            if (null !== $requestUri) {
                return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
            }

            $origPathInfo = $server['ORIG_PATH_INFO'] ?? null;
            if (empty($origPathInfo)) {
                return '/';
            }

            return $origPathInfo;
        };

        // URI scheme
        $scheme = 'http';
        $marshalHttpsValue = static function ($https): bool {
            if (is_bool($https)) {
                return $https;
            }

            if (!is_string($https)) {
                throw new \InvalidArgumentException(sprintf(
                    'SAPI HTTPS value MUST be a string or boolean; received %s',
                    gettype($https)
                ));
            }

            return 'on' === strtolower($https);
        };
        if (array_key_exists('HTTPS', $server)) {
            $https = $marshalHttpsValue($server['HTTPS']);
        } elseif (array_key_exists('https', $server)) {
            $https = $marshalHttpsValue($server['https']);
        } else {
            $https = false;
        }

        if ($https
            || 'https' === strtolower($getHeaderFromArray('x-forwarded-proto', $headers, ''))
        ) {
            $scheme = 'https';
        }

        // Set the host
        [$host, $port] = $marshalHostAndPort($headers, $server);

        // URI path
        $path = $marshalRequestPath($server);

        // Strip query string
        $path = explode('?', $path, 2)[0];

        // URI query
        $query = '';
        if (isset($server['QUERY_STRING'])) {
            $query = ltrim($server['QUERY_STRING'], '?');
        }

        // URI fragment
        $fragment = '';
        if (Str\contains($path, '#')) {
            [$path, $fragment] = Str\split($path, '#', 2);
        }

        return new self(
            $scheme,
            '',
            '',
            $host,
            (string) $port,
            $path,
            $query,
            $fragment
        );
    }
}
