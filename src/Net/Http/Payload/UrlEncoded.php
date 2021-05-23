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

namespace Castor\Net\Http\Payload;

use Castor\Io;
use Castor\Net\Http;

/**
 * Class UrlEncoded represents a url encoded payload in a Request Body.
 */
final class UrlEncoded extends Http\DecoratedBody implements Parser
{
    /**
     * @throws Io\Error
     */
    public function parse(): array
    {
        $parsed = [];
        parse_str(Io\readAll($this->body), $parsed);

        return $parsed;
    }
}
