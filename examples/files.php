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

$path = __DIR__.'/hello.txt';

if (!Castor\Fs\File::exists($path)) {
    $file = Castor\Fs\File::make($path);
} else {
    $file = Castor\Fs\File::open($path);
    $file->seek(0, Castor\Io\Seeker::END);
}
$file->write("This is some text\n");