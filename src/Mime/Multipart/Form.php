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

namespace Castor\Mime\Multipart;

/**
 * Class Multipart.
 */
class Form
{
    /**
     * @var string[]
     */
    private array $values;
    /**
     * @var FileHeader[]
     */
    private array $files;

    /**
     * Form constructor.
     */
    public function __construct()
    {
        $this->values = [];
        $this->files = [];
    }

    public function addValue(string $name, string $value): void
    {
        $this->values[$name] = $value;
    }

    public function addFile(string $name, FileHeader $file): void
    {
        $this->files[$name] = $file;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @throws FileNotFound
     */
    public function getFile(string $name): FileHeader
    {
        $file = $this->files[$name] ?? null;
        if (!$file instanceof FileHeader) {
            throw new FileNotFound(sprintf('File with key "%s" could not be found', $name));
        }

        return $file;
    }

    /**
     * @throws ValueNotFound
     */
    public function getValue(string $name): string
    {
        $value = $this->values[$name] ?? null;
        if (null === $value) {
            throw new ValueNotFound(sprintf('Value with key "%s" could not be found', $name));
        }

        return $value;
    }
}
