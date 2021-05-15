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

use Castor\Io\ReadCloser;
use Castor\Net\Uri;

/**
 * Class Request.
 */
class Request
{
    protected const COOKIES_ATTR = '_COOKIES';

    private string $method;
    private Uri $uri;
    private Protocol $protocol;
    private Headers $headers;
    private ReadCloser $body;
    private Context $context;

    /**
     * Request constructor.
     */
    public function __construct(string $method, Uri $uri, Protocol $protocol, Headers $headers, ReadCloser $body, Context $context)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->protocol = $protocol;
        $this->headers = $headers;
        $this->body = $body;
        $this->context = $context;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function isMethod(string $method): bool
    {
        return $this->method === $method;
    }

    public function getUri(): Uri
    {
        return $this->uri;
    }

    public function getProtocol(): Protocol
    {
        return $this->protocol;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    public function getBody(): ReadCloser
    {
        return $this->body;
    }

    public function getCookies(): array
    {
        return $this->context->get(self::COOKIES_ATTR) ?? [];
    }

    public function getCookie(string $name): ?string
    {
        return $this->context->get(self::COOKIES_ATTR)[$name] ?? null;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    private function parseCookies(): void
    {
        if (!$this->context->has(self::COOKIES_ATTR)) {
            $cookies = [];
            $cookieHeader = $this->headers->read('Cookie') ?? '';
            $cookiesStr = explode(';', $cookieHeader);
            foreach ($cookiesStr as $cookieStr) {
                [$name, $value] = explode('=', $cookieStr, 2);
                $cookies[trim($name, ' ')] = trim($value, ' "');
            }
            $this->context->put(self::COOKIES_ATTR, $cookies);
        }
    }
}
