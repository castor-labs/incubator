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

namespace Castor\Fiber;

use Castor\Io;
use Castor\Net\Http;
use Castor\Os;

/**
 * Class Context.
 */
interface Context
{
    public const PARAMS_ATTR = '_PARAMS';
    public const PATH_ATTR = '_PATH';
    public const ALLOWED_METHODS_ATTR = '_ALLOWED_METHODS';
    public const PARSED_BODY_ATTR = '_PARSED_BODY';

    /**
     * Returns the underlying writer for this connection.
     */
    public function getWriter(): Http\ResponseWriter;

    /**
     * Returns the request for this connection.
     */
    public function getRequest(): Http\Request;

    /**
     * Returns an specific routing param.
     */
    public function getParam(string $name): ?string;

    /**
     * Returns the routing params as an array.
     */
    public function getParams(): array;

    /**
     * Returns the parsed body as an array.
     */
    public function getParsedBody(): array;

    /**
     * Gets the session from the current context.
     */
    public function getSession(): Session;

    /**
     * Writes an html response to the underlying connection.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function html(string $html, int $status = Http\STATUS_OK): void;

    /**
     * Writes a json response to the underlying connection.
     *
     * @param mixed $data
     *
     * @throws Io\Error if the writing operation fails
     */
    public function json($data, array $context = [], int $status = Http\STATUS_OK): void;

    /**
     * Renders a view using a template engine.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function view(string $template, array $context = [], int $status = Http\STATUS_OK): void;

    /**
     * Sends a file to the client.
     *
     * If $downloadName is provided, the file will be sent as an attachment.
     *
     * If $downloadName is an empty string, then the filename of the provided path will be used.
     *
     * @throws Os\Error if the $path provided is not a file
     */
    public function file(string $path, string $downloadName = null, int $status = Http\STATUS_OK): void;

    /**
     * Writes a plain text response to the underlying connection.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function text(string $text, int $status = Http\STATUS_OK): void;

    /**
     * Writes a redirect response to the underlying connection.
     *
     * @throws Io\Error if the writing operation fails
     */
    public function redirect(string $uri): void;
}
