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

require_once __DIR__.'/../vendor/autoload.php';

$conn = Castor\Net\Socket::dial('tcp', '93.184.216.34:80');

$bytes = '';
$conn->write("GET / HTTP/1.1\nHost: example.com\nUser-Agent: curl/7.54.0\nAccept: */*\n\n");
$conn->read($bytes, 4096 * 5);

echo $bytes."\n";

$conn = Castor\Net\Socket::dial('unix', '/var/run/docker.sock');

$bytes = '';
$conn->write("GET /v1.24/containers/json HTTP/1.1\nHost: localhost\nUser-Agent: curl/7.54.0\nAccept: */*\n\n");
$conn->read($bytes, 4096 * 5);

echo $bytes;
