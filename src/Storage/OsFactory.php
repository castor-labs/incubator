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

use Castor\Net\Uri;
use Castor\Os;

/**
 * Class OsFactory.
 */
final class OsFactory implements Factory
{
    /**
     * {@inheritDoc}
     */
    public function create(Uri $uri): Driver
    {
        if ('os' !== $uri->getScheme()) {
            throw new UnsupportedScheme(sprintf('Unsupported scheme "%s" for %s', $uri->getScheme(), __CLASS__));
        }
        $folder = $uri->getPath();
        Os\ensureDir($folder);

        return new OsDriver($folder);
    }
}
