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

namespace Castor\Mime;

/**
 * Class LinuxRegistry.
 *
 * This class is a singleton that contains information about mime types in
 * the underlying linux system.
 *
 * Most distributions contain a /etc/mime.types file. For alpine based distros,
 * the mailcap package needs to be installed in order to make that file available.
 *
 * This registry parses that file and stores in memory the mime information.
 */
final class LinuxRegistry implements Registry
{
    private const FILE = '/etc/mime.types';

    private array $extensions;
    private array $mimeTypes;

    /**
     * Registry constructor.
     */
    private function __construct()
    {
        $this->extensions = [];
        $this->mimeTypes = [];
    }

    /**
     * Parses the mime registry located in the /etc/mime.types file.
     */
    public static function parse(): LinuxRegistry
    {
        $registry = new self();
        if (PHP_OS !== 'Linux') {
            return $registry;
        }
        if (!is_file(self::FILE) || !is_readable(self::FILE)) {
            return $registry;
        }

        $steam = fopen(self::FILE, 'rb');
        while (!feof($steam)) {
            $line = fgets($steam);
            if (!is_string($line)) {
                continue;
            }
            $line = trim($line, "\r\n");
            if ('' === $line || 0 === strpos($line, '#')) {
                continue;
            }
            // Normalize tab characters
            $line = preg_replace('/\s+/', ' ', $line);
            $parts = explode(' ', $line);
            $mime = array_shift($parts);
            $registry->register($mime, ...$parts);
        }

        return $registry;
    }

    /**
     * @param string ...$extensions
     */
    public function register(string $mimeType, string ...$extensions): void
    {
        $this->mimeTypes[$mimeType] = $extensions;
        foreach ($extensions as $extension) {
            $this->extensions[$extension] = $mimeType;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensions(string $mimeType): array
    {
        return $this->mimeTypes[$mimeType] ?? [];
    }

    /**
     * {@inheritDoc}
     */
    public function getExtension(string $mimeType): ?string
    {
        return $this->getExtensions($mimeType)[0] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function getMimeType(string $extension): ?string
    {
        return $this->extensions[$extension] ?? null;
    }
}
