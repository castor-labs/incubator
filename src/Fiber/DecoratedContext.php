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

/**
 * Class DecoratedContext.
 */
abstract class DecoratedContext implements Context
{
    protected Context $context;

    /**
     * DecoratedContext constructor.
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     */
    public function getWriter(): Http\ResponseWriter
    {
        return $this->context->getWriter();
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest(): Http\Request
    {
        return $this->context->getRequest();
    }

    /**
     * {@inheritDoc}
     */
    public function getParsedBody(): array
    {
        return $this->context->getParsedBody();
    }

    /**
     * {@inheritDoc}
     */
    public function getParam(string $name): ?string
    {
        return $this->context->getParam($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getParams(): array
    {
        return $this->context->getParams();
    }

    /**
     * {@inheritDoc}
     */
    public function getSession(): Session
    {
        return $this->context->getSession();
    }

    /**
     * {@inheritDoc}
     */
    public function html(string $html, int $status = Http\STATUS_OK): void
    {
        $this->context->html($html, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function json($data, array $context = [], int $status = Http\STATUS_OK): void
    {
        $this->context->json($data, $context, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function view(string $template, array $context = [], int $status = Http\STATUS_OK): void
    {
        $this->context->view($template, $context, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function file(string $path, string $downloadName = null, int $status = Http\STATUS_OK): void
    {
        $this->context->file($path, $downloadName, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function text(string $text, int $status = Http\STATUS_OK): void
    {
        $this->context->text($text, $status);
    }

    /**
     * {@inheritDoc}
     */
    public function redirect(string $uri): void
    {
        $this->context->redirect($uri);
    }

    public function getInnerContext(): Context
    {
        return $this->context;
    }
}
