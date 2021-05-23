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

/**
 * Class DecoratedBlob.
 */
abstract class DecoratedBlob implements Blob
{
    private Blob $blob;

    /**
     * DecoratedBlob constructor.
     */
    public function __construct(Blob $blob)
    {
        $this->blob = $blob;
    }

    public function getInnerBlob(): Blob
    {
        return $this->blob;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->blob->read($bytes, $length);
    }

    /**
     * {@inheritDoc}
     */
    public function size(): int
    {
        return $this->blob->size();
    }

    /**
     * {@inheritDoc}
     */
    public function getMimeType(): string
    {
        return $this->blob->getMimeType();
    }
}
