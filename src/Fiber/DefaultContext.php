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

use Castor\Net\Http\Request;
use Castor\Net\Http\ResponseWriter;
use const Castor\Net\Http\STATUS_FOUND;
use const Castor\Net\Http\STATUS_OK;
use Castor\Template\Engine;
use Castor\Template\PhpEngine;
use JsonException;

/**
 * Class BaseContext.
 */
final class DefaultContext implements Context
{
    private ResponseWriter $writer;
    private Request $request;
    private ?PhpEngine $engine;

    /**
     * BaseContext constructor.
     */
    public function __construct(ResponseWriter $writer, Request $request, Engine $engine = null)
    {
        $this->writer = $writer;
        $this->request = $request;
        $this->engine = $engine;
    }

    /**
     * {@inheritDoc}
     */
    public function getWriter(): ResponseWriter
    {
        return $this->writer;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function html(string $html, int $status = STATUS_OK): void
    {
        $this->writer->getHeaders()->add('Content-Type', 'text/html');
        $this->writer->getHeaders()->add('Content-Length', (string) strlen($html));
        $this->writer->writeHeaders($status);
        $this->writer->write($html);
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonException if $data cannot be encoded into json
     */
    public function json($data, array $context = [], int $status = STATUS_OK): void
    {
        $json = json_encode($data, JSON_THROW_ON_ERROR);
        $this->writer->getHeaders()->add('Content-Type', 'application/json');
        $this->writer->getHeaders()->add('Content-Length', (string) strlen($json));
        $this->writer->writeHeaders($status);
        $this->writer->write($json);
    }

    /**
     * {@inheritDoc}
     */
    public function text(string $text, int $status = STATUS_OK): void
    {
        $this->writer->getHeaders()->add('Content-Type', 'text/plain');
        $this->writer->getHeaders()->add('Content-Length', (string) strlen($text));
        $this->writer->writeHeaders($status);
        $this->writer->write($text);
    }

    /**
     * {@inheritDoc}
     */
    public function view(string $template, array $context = [], int $status = STATUS_OK): void
    {
        if (null === $this->engine) {
            throw new \RuntimeException('There is no configured template engine');
        }
        $view = $this->engine->render($template, $context);
        $this->writer->getHeaders()->add('Content-Type', 'text/html');
        $this->writer->writeHeaders($status);
        $view->writeTo($this->writer);
    }

    /**
     * {@inheritDoc}
     */
    public function redirect(string $uri): void
    {
        $this->writer->getHeaders()->add('Location', $uri);
        $this->writer->writeHeaders(STATUS_FOUND);
        $this->writer->write('');
    }
}
