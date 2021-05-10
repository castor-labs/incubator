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

namespace Castor\Net\Http\Cgi;

use Castor\Io;
use Castor\Net\Http;

/**
 * The serve method runs a handler in a CGI context.
 *
 * It parses the request from the available super globals and provides a response
 * writer that handles sending the header and the content back to the client.
 *
 * It also processes uploaded files and keys if the request is of the multipart
 * content type.
 *
 * If unset globals is true, after parsing the Request information, the
 * super globals are removed from scope.
 *
 * @throws Io\Error
 */
function serve(Http\Handler $handler, bool $unsetGlobals = true): void
{
    if (PHP_SAPI === 'cli') {
        throw new Io\Error('Cannot serve in a non CGI context');
    }
    $writer = ResponseWriter::create();
    $request = Request::fromGlobals();
    if ($unsetGlobals) {
        unset($_GET, $_POST, $_FILES, $_SERVER, $_SESSION, $_REQUEST);
    }

    $handler->handleHTTP($writer, $request);
    $writer->flush();
    $request->getBody()->close();
}
