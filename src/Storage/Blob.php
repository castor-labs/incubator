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

namespace Castor\Storage;

use Castor\Io;
use Castor\Mime;

/**
 * A Blob represents some bytes that have a predefined size and a mime type.
 */
interface Blob extends Io\Reader, Mime\Type, Io\Sizer
{
}
