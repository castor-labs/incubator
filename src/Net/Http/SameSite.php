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

namespace Castor\Net\Http;

/**
 * Class SameSite.
 */
class SameSite implements \Stringable
{
    public const STRICT = 'Strict';
    public const LAX = 'Lax';
    public const NONE = 'None';

    public string $value;

    /**
     * SameSite constructor.
     */
    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->toStr();
    }

    public static function lax(): SameSite
    {
        return new self(self::LAX);
    }

    public static function strict(): SameSite
    {
        return new self(self::STRICT);
    }

    public static function none(): SameSite
    {
        return new self(self::NONE);
    }

    public function equals(SameSite $sameSite): bool
    {
        return $this->value === $sameSite->value;
    }

    public function toStr(): string
    {
        return $this->value;
    }
}
