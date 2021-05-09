<?php

declare(strict_types=1);

/**
 * @project Castor Io
 * @link https://github.com/castor-labs/io
 * @package castor/io
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Net\Uri;

use Castor\Str;

/**
 * Class Scheme.
 */
final class Scheme extends Str
{
    /**
     * @var array|string[]
     * @psalm-var array<string,string>
     */
    private static array $map = [
        'http' => '80',
        'https' => '443',
        'ftp' => '21',
        'ftps' => '990',
        'smtp' => '25',
    ];

    private static array $secure = ['https', 'ftps'];

    public function __construct(string $string)
    {
        $string = strtolower($string);
        parent::__construct($string);
    }

    /**
     * Returns the default port for this scheme.
     *
     * Returns an empty string if the port is not known
     */
    public function getDefaultPort(): string
    {
        return self::$map[$this->string] ?? '';
    }

    public function isSecure(): bool
    {
        return in_array($this->string, self::$secure, true);
    }
}
