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

namespace Castor\Storage;

/**
 * Interface Driver represents an Storage Driver.
 *
 * Implementation is kept extremely simple with just for operations to avoid leaky
 * abstractions.
 *
 * Implementors CAN define more methods than these and leave up to userland the
 * checking of the corresponding methods of that Driver instance.
 *
 * Ex:
 * ```
 * if ($driver instanceof S3Driver) {
 *    return $driver->signedUrl($path);
 * }
 * ```
 *
 * Implementors MUST implement extra behaviour like ACL or others by using a
 * DecoratedBlob, and adding extra config into that class. This is because
 * specific configuration in an implementation detail.
 */
interface Driver
{
    /**
     * Puts a blob into the specified path.
     *
     * Implementors MUT make this operation idempotent.
     *
     * @throws Error if the blob could not be stored
     */
    public function put(string $path, Blob $blob): void;

    /**
     * Gets a blob from the specified path.
     *
     * @throws Error        if the blob could not be obtained
     * @throws PathNotFound if the path does not exist
     */
    public function get(string $path): Blob;

    /**
     * Deletes a path.
     *
     * Implementors MUST make this operation idempotent.
     *
     * @throws Error if the path could not be deleted
     */
    public function delete(string $path): void;

    /**
     * Moves a path to another.
     *
     * @throws Error if the blob could not be moved
     */
    public function move(string $path, string $newPath): void;
}
