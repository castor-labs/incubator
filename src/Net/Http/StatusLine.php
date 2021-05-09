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

/**
 * Class StatusLine represents the status line in an HTTP Response.
 *
 * It contains the protocol version, the status code and the reason phrase.
 */
class StatusLine
{
    private float $version;
    private string $code;
    private string $phrase;

    public function __construct(float $version, string $code, string $phrase)
    {
        $this->version = $version;
        $this->code = $code;
        $this->phrase = $phrase;
    }

    public function getVersion(): float
    {
        return $this->version;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPhrase(): string
    {
        return $this->phrase;
    }

    public function isCode(int $code): bool
    {
        return $this->code === $code;
    }

    /**
     * Returns true if the status code is in the specified range.
     *
     * Start and end values are considered to be inside the range.
     */
    public function inBetween(int $start, int $end): bool
    {
        return $this->code >= $start && $this->code <= $end;
    }

    /**
     * Returns true if the status code is in the 200 range.
     */
    public function isSuccess(): bool
    {
        return $this->inBetween(200, 299);
    }

    /**
     * Returns true if the status code is in the 300 range.
     */
    public function isRedirect(): bool
    {
        return $this->inBetween(300, 399);
    }

    /**
     * Returns true if the status code in in the 500 range.
     */
    public function isServerError(): bool
    {
        return $this->inBetween(500, 599);
    }

    /**
     * Returns true if the status code in in the 400 range.
     */
    public function isClientError(): bool
    {
        return $this->inBetween(400, 499);
    }

    /**
     * Returns true if the status code is in the 400 or 500 range.
     */
    public function isError(): bool
    {
        return $this->isClientError() || $this->isServerError();
    }
}
