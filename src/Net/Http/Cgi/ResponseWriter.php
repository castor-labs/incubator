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

use Castor\Net\Http;
use Castor\Net\Http\Headers;

/**
 * The ResponseWriter is a wrapper over PHP's CGI response providing functions.
 *
 * It also implements flusher since the CGI environment buffers the
 * responses before sending them to the client.
 *
 * Flushing data immediately increases protocol wire overload, but it can be
 * efficient for handling special cases, like sending text/event-stream responses.
 */
final class ResponseWriter implements Http\ResponseWriter, Http\Flusher
{
    private Headers $headers;
    private bool $sentHeaders;

    /**
     * CgiResponseWriter constructor.
     */
    private function __construct(Headers $headers)
    {
        $this->headers = $headers;
        $this->sentHeaders = false;
    }

    public static function create(): ResponseWriter
    {
        return new self(new Headers());
    }

    public function write(string $bytes): int
    {
        if (false === $this->sentHeaders) {
            $this->writeHeaders();
        }
        echo $bytes;

        return strlen($bytes);
    }

    public function writeHeaders(int $statusCode = 200): void
    {
        if ($this->sentHeaders) {
            throw new \RuntimeException('Headers already sent');
        }
        $headers = $this->headers->all();
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                header($name.': '.$value, false, $statusCode);
            }
        }
        $this->sentHeaders = true;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function flush(): void
    {
        ob_flush();
        flush();
    }
}
