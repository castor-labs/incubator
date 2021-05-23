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

namespace Castor\Mime\Multipart;

use Closure;

/**
 * Class FileHeader represents a header for a multipart file.
 */
class FileHeader
{
    private string $filename;
    private string $type;
    private int $size;
    private Closure $open;

    /**
     * FileHeader constructor.
     */
    public function __construct(string $filename, string $type, int $size, callable $open)
    {
        $this->filename = $filename;
        $this->type = $type;
        $this->size = $size;
        $this->open = $this->memo($open);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function open(): File
    {
        return ($this->open)();
    }

    private function memo(callable $open): Closure
    {
        return static function () use ($open): File {
            static $file;
            if (null === $file) {
                $file = $open();
            }

            return $file;
        };
    }
}
