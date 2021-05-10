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

namespace Castor\Fiber\Extension;

use Castor\Fiber\Context;
use Castor\Net\Http;
use Castor\Template;

/**
 * Class TemplateEngine.
 */
final class TemplateEngine implements Context
{
    private Context $context;
    private Template\Engine $engine;

    /**
     * LocalFileSupport constructor.
     */
    public function __construct(Context $context, Template\Engine $engine)
    {
        $this->context = $context;
        $this->engine = $engine;
    }

    public function getWriter(): Http\ResponseWriter
    {
        return $this->context->getWriter();
    }

    public function getRequest(): Http\Request
    {
        return $this->context->getRequest();
    }

    public function html(string $html, int $status = Http\STATUS_OK): void
    {
        $this->context->html($html, $status);
    }

    public function header(string $name, string $value): void
    {
        $this->context->header($name, $value);
    }

    public function json($data, array $context = [], int $status = Http\STATUS_OK): void
    {
        $this->context->json($data, $context, $status);
    }

    public function view(string $template, array $context = [], int $status = Http\STATUS_OK): void
    {
        $context = array_merge($this->getRequest()->getContext()->all(), $context);
        $view = $this->engine->render($template, $context);
        $this->header('Content-Type', 'text/html');
        $this->context->getWriter()->writeHeaders($status);
        $view->writeTo($this->getWriter());
    }

    public function text(string $text, int $status = Http\STATUS_OK): void
    {
        $this->context->text($text, $status);
    }

    public function file(string $path, string $name = null, int $status = Http\STATUS_OK): void
    {
        $this->context->file($path, $name, $status);
    }

    public function redirect(string $uri): void
    {
        $this->context->redirect($uri);
    }
}
