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

namespace Castor\Fiber;

use Castor\Io;
use Castor\Net\Http;
use const Castor\Net\Http\STATUS_OK;
use InvalidArgumentException;

/**
 * Class Context.
 */
interface Context
{
    public const PARAMS_ATTR = '_PARAMS';
    public const PATH_ATTR = '_PATH';
    public const ALLOWED_METHODS_ATTR = '_ALLOWED_METHODS';

    /**
     * Returns the underlying writer for this connection.
     */
    public function getWriter(): Http\ResponseWriter;

    /**
     * Returns the request for this connection.
     */
    public function getRequest(): Http\Request;

    /**
     * Writes an html response to the underlying connection.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function html(string $html, int $status = STATUS_OK): void;

    /**
     * Writes a json response to the underlying connection.
     *
     * @param mixed $data
     *
     * @throws Io\Error if the writing operation fails
     */
    public function json($data, array $context = [], int $status = STATUS_OK): void;

    /**
     * Renders a view using a template engine.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function view(string $template, array $context = [], int $status = STATUS_OK): void;

    /**
     * Sends a file to the client.
     *
     * If $downloadName is provided, the file will be sent as an attachment.
     *
     * If $downloadName is an empty string, then the filename of the provided path will be used.
     *
     * @throws InvalidArgumentException if the $path provided is not a file
     */
    public function file(string $path, string $downloadName = null, int $status = STATUS_OK): void;

    public function getParam(string $name): ?string;

    public function getParams(): array;

    /**
     * Writes a plain text response to the underlying connection.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function text(string $text, int $status = STATUS_OK): void;

    /**
     * Writes a redirect response to the underlying connection.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function redirect(string $uri): void;
}
