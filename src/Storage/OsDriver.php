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
use Castor\Os;

/**
 * Class OsDriver.
 */
final class OsDriver implements Driver
{
    private string $path;

    /**
     * OsDriver constructor.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function put(string $path, Blob $blob): void
    {
        $filename = Os\Path\join($this->path, $path);
        $file = Os\File::put($filename);
        Io\copy($blob, $file);
    }

    public function get(string $path): Blob
    {
        $filename = Os\Path\join($this->path, $path);

        try {
            $file = Os\File::open($filename);
        } catch (Os\Error $e) {
            throw new PathNotFound(sprintf('No blob found in path %s', $path));
        }

        return new ReaderBlob(
            $file,
            $file->getMimeType(),
            $file->size()
        );
    }

    public function delete(string $path): void
    {
        $filename = Os\Path\join($this->path, $path);
        Os\remove($filename);
    }

    public function move(string $path, string $newPath): void
    {
        $filename = Os\Path\join($this->path, $path);
        $newFilename = Os\Path\join($this->path, $path);
        Os\copy($filename, $newFilename);
        Os\remove($filename);
    }
}
