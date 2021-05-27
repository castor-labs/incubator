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

namespace Castor\Dapper\Handler;

/**
 * Class ClassSuffixInflector.
 */
final class ClassSuffixInflector implements Inflector
{
    private string $suffix;

    /**
     * ClassSuffixInflector constructor.
     */
    public function __construct(string $suffix = 'Handler')
    {
        $this->suffix = $suffix;
    }

    /**
     * {@inheritDoc}
     */
    public function inflect(object $command): string
    {
        $cmdClass = get_class($command);
        $handlerClass = $cmdClass.$this->suffix;
        if (!class_exists($handlerClass)) {
            throw new \RuntimeException(sprintf(
                'Handler class %s for command %s does not exist',
                $handlerClass,
                $cmdClass
            ));
        }

        return $handlerClass;
    }
}
