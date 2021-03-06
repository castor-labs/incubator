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
 * Class Json represents a Json payload in a Request Body.
 */
final class Json extends Http\DecoratedBody implements Parser
{
    /**
     * {@inheritDoc}
     *
     * @throws \JsonException
     */
    public function parse(): array
    {
        return json_decode(Io\readAll($this->body), true, 512, JSON_THROW_ON_ERROR);
    }
}
