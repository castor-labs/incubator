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

namespace Castor\Net\Http;

// Success Codes
const STATUS_OK = 200;
const STATUS_CREATED = 201;
const STATUS_ACCEPTED = 202;
const STATUS_NON_AUTHORITATIVE_INFORMATION = 203;
const STATUS_NO_CONTENT = 204;
const STATUS_RESET_CONTENT = 205;
const STATUS_PARTIAL_CONTENT = 206;
// Redirection Code
const STATUS_MULTIPLE_CHOICE = 300;
const STATUS_MOVED_PERMANENTLY = 301;
const STATUS_FOUND = 302;
const STATUS_SEE_OTHER = 303;
const STATUS_NOT_MODIFIED = 304;
const STATUS_TEMPORARY_REDIRECT = 307;
const STATUS_PERMANENT_REDIRECT = 308;

// Client Error Codes
const STATUS_BAD_REQUEST = 400;
const STATUS_UNAUTHORIZED = 401;
const STATUS_PAYMENT_REQUIRED = 402;
const STATUS_FORBIDDEN = 403;
const STATUS_NOT_FOUND = 404;
const STATUS_METHOD_NOT_ALLOWED = 405;
const STATUS_NOT_ACCEPTABLE = 406;
const STATUS_PROXY_AUTHENTICATION_REQUIRED = 407;
const STATUS_REQUEST_TIMEOUT = 408;
const STATUS_CONFLICT = 409;
const STATUS_GONE = 410;
const STATUS_LENGTH_REQUIRED = 411;
const STATUS_PRECONDITION_FAILED = 412;
const STATUS_PAYLOAD_TOO_LARGE = 413;
const STATUS_URI_TOO_LONG = 414;
const STATUS_UNSUPPORTED_MEDIA_TYPE = 415;
const STATUS_RANGE_NOT_SATISFIABLE = 416;
const STATUS_EXPECTATION_FAILED = 417;
const STATUS_IM_A_TEAPOT = 418;
const STATUS_MISDIRECTED_REQUEST = 421;
const STATUS_UNPROCESSABLE_ENTITY = 422;
const STATUS_LOCKED = 423;
const STATUS_FAILED_DEPENDENCY = 424;
const STATUS_TOO_EARLY = 425;
const STATUS_UPGRADE_REQUIRED = 426;
const STATUS_PRECONDITION_REQUIRED = 428;
const STATUS_TOO_MANY_REQUESTS = 429;
const STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
const STATUS_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
// Server Error Codes
const STATUS_INTERNAL_SERVER_ERROR = 500;
const STATUS_NOT_IMPLEMENTED = 501;
const STATUS_BAD_GATEWAY = 502;
const STATUS_SERVICE_UNAVAILABLE = 503;
const STATUS_GATEWAY_TIMEOUT = 504;
const STATUS_HTTP_VERSION_NOT_SUPPORTED = 505;
const STATUS_VARIANT_ALSO_NEGOTIATES = 506;
const STATUS_NOT_EXTENDED = 510;
const STATUS_NETWORK_AUTHENTICATION_REQUIRED = 511;

/**
 * Transforms a callable into a Handler.
 */
function handlerFunc(callable $handler): Handler
{
    return new CallableHandler($handler);
}

/**
 * This function creates a stack of handlers from a base handler and
 * an array of handler factories.
 *
 * Handler factories are callables that take the next handler as an argument and
 * return another handler.
 *
 * @param callable ...$factories
 */
function stack(Handler $handler, callable ...$factories): Handler
{
    return array_reduce(
        array_reverse($factories),
        static function (Handler $previous, callable $factory) {
            return $factory($previous);
        },
        $handler,
    );
}

function setCookie(ResponseWriter $writer, Cookie ...$cookies): void
{
    foreach ($cookies as $cookie) {
        $writer->getHeaders()->add('Set-Cookie', $cookie->toHttpString());
    }
}
