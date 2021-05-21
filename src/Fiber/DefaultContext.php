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

use Castor\Net\Http;
use Castor\Os;
use Castor\Template;
use JsonException;

/**
 * Class BaseContext.
 */
final class DefaultContext implements Context
{
    private Http\ResponseWriter $writer;
    private Http\Request $request;
    private ?Template\Engine $engine;

    /**
     * BaseContext constructor.
     */
    public function __construct(Http\ResponseWriter $writer, Http\Request $request, Template\Engine $engine = null)
    {
        $this->writer = $writer;
        $this->request = $request;
        $this->engine = $engine;
    }

    public function getParam(string $name): ?string
    {
        return $this->getParams()[$name] ?? null;
    }

    public function getParams(): array
    {
        return $this->request->getContext()->get(self::PARAMS_ATTR) ?? [];
    }

    public function getParsedBody(): array
    {
        $parsedBody = $this->request->getContext()->get(self::PARSED_BODY_ATTR) ?? [];
        $body = $this->request->getBody();
        if ([] === $parsedBody && $body instanceof Http\Payload\Parser) {
            $parsedBody = $body->parse();
            $this->request->getContext()->put(self::PARSED_BODY_ATTR, $parsedBody);
        }

        return $parsedBody;
    }

    /**
     * {@inheritDoc}
     */
    public function getWriter(): Http\ResponseWriter
    {
        return $this->writer;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): Http\Request
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function html(string $html, int $status = Http\STATUS_OK): void
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
    public function json($data, array $context = [], int $status = Http\STATUS_OK): void
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
    public function text(string $text, int $status = Http\STATUS_OK): void
    {
        $this->writer->getHeaders()->add('Content-Type', 'text/plain');
        $this->writer->getHeaders()->add('Content-Length', (string) strlen($text));
        $this->writer->writeHeaders($status);
        $this->writer->write($text);
    }

    /**
     * {@inheritDoc}
     */
    public function file(string $path, string $downloadName = null, int $status = Http\STATUS_OK): void
    {
        $file = Os\File::open($path);
        $this->writer->getHeaders()->add('Content-Type', $file->getMimeType());
        $this->writer->getHeaders()->add('Content-Length', (string) $file->size());
        if ('' === $downloadName) {
            $downloadName = Os\Path\filename($path);
        }
        if (null !== $downloadName) {
            $this->writer->getHeaders()->add('Content-Disposition', sprintf('attachment; filename="%s"', $downloadName));
        }
        $file->writeTo($this->writer);
    }

    /**
     * {@inheritDoc}
     */
    public function view(string $template, array $context = [], int $status = Http\STATUS_OK): void
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
        $this->writer->writeHeaders(Http\STATUS_FOUND);
    }
}
