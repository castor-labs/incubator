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

use Castor\Io\Writer;

/**
 * A ResponseWriter is used by an HTTP Handler to construct an HTTP Response.
 */
interface ResponseWriter extends Writer
{
    /**
     * Returns the in memory headers that will be written upon the writeHeaders
     * call.
     *
     * Changing the header map after a call to writeHeaders (or write) has no
     * effect unless the modified headers are trailers.
     */
    public function getHeaders(): Headers;

    /**
     * Write Headers writes the headers part of an HTTP response with a valid
     * status code in the 1xx - 5xx range.
     *
     * If writeHeaders is not called explicitly, the first call to write will
     * trigger an implicit `writeHeaders(Castor\Net\Http::STATUS_OK)` call. Thus,
     * explicit calls to `writeHeaders` are mainly used for sending redirects or
     * errors.
     */
    public function writeHeaders(int $statusCode): void;
}
