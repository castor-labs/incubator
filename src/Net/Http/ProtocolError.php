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

namespace Castor\Net\Http;

use Throwable;

/**
 * Class ProtocolError.
 */
class ProtocolError extends \Exception
{
    /**
     * ProtocolError constructor.
     *
     * @param string $message
     */
    public function __construct(int $status = STATUS_INTERNAL_SERVER_ERROR, $message = '', Throwable $previous = null)
    {
        parent::__construct($message, $status, $previous);
    }

    public function isCode(int $code): bool
    {
        return $this->code === $code;
    }
}
