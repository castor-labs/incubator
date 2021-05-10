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
use Castor\Fs\File;
use Castor\Net\Http;

/**
 * Class LocalFileSupport.
 */
final class LocalFile implements Context
{
    private Context $context;
    private string $path;

    /**
     * LocalFileSupport constructor.
     */
    public function __construct(Context $context, string $path)
    {
        $this->context = $context;
        $this->path = $path;
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
        $this->context->view($template, $context, $status);
    }

    public function text(string $text, int $status = Http\STATUS_OK): void
    {
        $this->context->text($text, $status);
    }

    public function file(string $path, string $name = null, int $status = Http\STATUS_OK): void
    {
        $filename = $this->path.DIRECTORY_SEPARATOR.ltrim($path, '/');
        if (!File::exists($filename)) {
            throw new Http\ProtocolError(Http\STATUS_NOT_FOUND, 'Could not find file');
        }
        $file = File::open($filename);
        $this->header('Content-Type', $file->getContentType());
        $this->header('Content-Length', (string) $file->getSize());
        $disposition = 'inline';
        if (null !== $name) {
            $disposition = sprintf('attachment; filename="%s"', $name);
        }
        $this->header('Content-Disposition', $disposition);
        $this->getWriter()->writeHeaders($status);
        $file->writeTo($this->getWriter());
    }

    public function redirect(string $uri): void
    {
        $this->context->redirect($uri);
    }
}
