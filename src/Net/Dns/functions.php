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

namespace Castor\Net\Dns;

/**
 * Normalizes a DNS name.
 *
 * @throws InvalidName
 */
function normalize(string $name): string
{
    static $pattern = '/^(?<name>[a-z0-9]([a-z0-9-_]{0,61}[a-z0-9])?)(\.(?&name))*\.?$/i';

    if (\function_exists('idn_to_ascii') && \defined('INTL_IDNA_VARIANT_UTS46')) {
        if (false === $result = \idn_to_ascii($name, 0, \INTL_IDNA_VARIANT_UTS46)) {
            throw new InvalidName("Name '{$name}' could not be processed for IDN.");
        }

        $name = $result;
    } else {
        if (\preg_match('/[\x80-\xff]/', $name)) {
            throw new InvalidName(
                "Name '{$name}' contains non-ASCII characters and IDN support is not available. ".
    'Verify that ext/intl is installed for IDN support and that ICU is at least version 4.6.'
            );
        }
    }

    if (isset($name[253]) || !\preg_match($pattern, $name)) {
        throw new InvalidName("Name '{$name}' is not a valid hostname.");
    }

    if ('.' === $name[strlen($name) - 1]) {
        $name = substr($name, 0, -1);
    }

    return $name;
}
