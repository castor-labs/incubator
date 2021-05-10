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

namespace Castor\Net\Http\Cgi;

use Castor\Mime\Multipart\FileHeader;
use Castor\Mime\Multipart\Form;
use Castor\Mime\Multipart\TmpFile;
use Castor\Net\Http;

/**
 * Class Request represents a CGI request.
 *
 * Since the PHP CGI manages multipart uploads automatically, this class wraps
 * the request body in a multipart by itself.
 *
 * It also contains other variables in the multipart process.
 */
final class Request extends Http\Request
{
    /**
     * Creates a Request from PHP CGI Globals.
     */
    public static function fromGlobals(): Request
    {
        $server = self::normalizeServer(
            $_SERVER,
            is_callable('apache_request_headers') ? 'apache_request_headers' : null
        );
        $method = self::marshalMethodFromSapi($server);
        $headers = self::marshalHeadersFromSapi($server);
        $body = new Body();
        if ([] !== $_FILES || $headers->contains('content-type', 'multipart')) {
            $form = self::parseFormFromSapi($_FILES, $_POST);
            $body = new Http\Payload\Multipart($body, $form);
        }
        $uri = Uri::marshalFromSapi($server, $headers->toMap());
        $protocol = self::marshalProtocolFromSapi($server);

        return new self($method, $uri, $protocol, $headers, $body, new Http\Context(['_server' => $_SERVER]));
    }

    private static function normalizeServer(array $server, callable $apacheRequestHeaderCallback = null): array
    {
        if (null === $apacheRequestHeaderCallback && is_callable('apache_request_headers')) {
            $apacheRequestHeaderCallback = 'apache_request_headers';
        }

        // If the HTTP_AUTHORIZATION value is already set, or the callback is not
        // callable, we return verbatim
        if (isset($server['HTTP_AUTHORIZATION'])
            || !is_callable($apacheRequestHeaderCallback)
        ) {
            return $server;
        }

        $apacheRequestHeaders = $apacheRequestHeaderCallback();
        if (isset($apacheRequestHeaders['Authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['Authorization'];

            return $server;
        }

        if (isset($apacheRequestHeaders['authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['authorization'];

            return $server;
        }

        return $server;
    }

    private static function marshalMethodFromSapi(array $server): string
    {
        return $server['REQUEST_METHOD'] ?? 'GET';
    }

    private static function marshalProtocolFromSapi(array $server): Http\Protocol
    {
        if (!isset($server['SERVER_PROTOCOL'])) {
            return new Http\Protocol(1, 1);
        }

        if (!preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $server['SERVER_PROTOCOL'], $matches)) {
            throw new \UnexpectedValueException('Unrecognized protocol');
        }

        return Http\Protocol::parse($matches['version']);
    }

    private static function marshalHeadersFromSapi(array $server): Http\Headers
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (!is_string($key)) {
                continue;
            }

            if ('' === $value) {
                continue;
            }

            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (str_starts_with($key, 'REDIRECT_')) {
                $key = substr($key, 9);

                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (array_key_exists($key, $server)) {
                    continue;
                }
            }

            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;

                continue;
            }

            if (str_starts_with($key, 'CONTENT_')) {
                $name = str_replace('_', '-', strtolower($key));
                $headers[$name] = $value;

                continue;
            }
        }

        return Http\Headers::fromMap($headers);
    }

    private static function parseFormFromSapi(array $files, array $post): Form
    {
        $form = new Form();
        foreach ($files as $name => $data) {
            $open = static fn () => TmpFile::open($data['tmp_name']);
            $file = new FileHeader($data['name'], $data['type'], $data['size'], $open);
            $form->addFile($name, $file);
        }
        foreach ($post as $name => $value) {
            $form->addValue($name, $value);
        }

        return $form;
    }
}
