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

namespace Castor\Net\Http\Payload;

use Castor\Io\Eof;
use Castor\Io\Error;
use Castor\Io\ReadCloser;
use Castor\Io\Reader;
use Castor\Mime;

/**
 * A Multipart wraps an Io\ReadCloser to contain multipart information.
 *
 * In CGI mode, multipart is handled by the upstream server, so there is no
 * parsing to do.
 *
 * In a PHP managed server, the file headers and contents need to be parsed and
 * put either in temporary files or in memory.
 */
final class Multipart implements ReadCloser, Parser
{
    private ReadCloser $reader;
    private Mime\Multipart\Form $form;

    /**
     * Multipart constructor.
     */
    public function __construct(ReadCloser $reader, Mime\Multipart\Form $form)
    {
        $this->reader = $reader;
        $this->form = $form;
    }

    /**
     * @throws Eof
     * @throws Error
     */
    public function read(string &$bytes, int $length = Reader::DEFAULT_READ_SIZE): int
    {
        return $this->reader->read($bytes, $length);
    }

    /**
     * @throws Error
     */
    public function close(): void
    {
        $this->reader->close();
    }

    public function getInnerBody(): ReadCloser
    {
        return $this->reader;
    }

    public function getForm(): Mime\Multipart\Form
    {
        return $this->form;
    }

    public function parse(): array
    {
        $files = $this->form->getFiles();
        $values = $this->form->getValues();

        return array_merge($files, $values);
    }
}
