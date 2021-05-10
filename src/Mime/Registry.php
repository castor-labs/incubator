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

namespace Castor\Mime;

/**
 * Class Registry.
 */
interface Registry
{
    /**
     * Gets the extensions associated with a mime type.
     */
    public function getExtensions(string $mimeType): array;

    /**
     * Gets the first extension associated with the mime type.
     */
    public function getExtension(string $mimeType): ?string;

    /**
     * Gets the mime type associated with the extension.
     */
    public function getMimeType(string $extension): ?string;
}
