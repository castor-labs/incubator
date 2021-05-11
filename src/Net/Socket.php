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

namespace Castor\Net;

use Castor\Io;

/**
 * Class Connection.
 */
final class Socket implements Io\Reader, Io\Writer
{
    use Io\ResourceHelper;

    /**
     * Connection constructor.
     *
     * @param $resource
     */
    protected function __construct($resource)
    {
        $this->setResource($resource);
    }

    /**
     * @throws SocketError
     */
    public static function dial(string $transport, string $address, int $timeout = 5): Socket
    {
        $errorNo = -1;
        $errStr = '';
        $path = $transport.'://'.$address;
        $resource = stream_socket_client($path, $errorNo, $errStr, $timeout);
        if (!is_resource($resource)) {
            throw new SocketError(sprintf('Could not connect to %s: %s', $path, $errStr), $errorNo);
        }

        return new self($resource);
    }

    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->innerRead($bytes, $length);
    }

    public function write(string $bytes): int
    {
        return $this->innerWrite($bytes);
    }
}
