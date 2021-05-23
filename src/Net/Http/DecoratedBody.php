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

use Castor\Io;

/**
 * Class DecoratedBody.
 */
abstract class DecoratedBody implements Io\ReadCloser
{
    protected Io\ReadCloser $body;

    /**
     * BodyDecorator constructor.
     */
    public function __construct(Io\ReadCloser $body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->body->close();
    }

    /**
     * {@inheritDoc}
     */
    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->body->read($bytes, $length);
    }

    public function getInnerBody(): Io\ReadCloser
    {
        return $this->body;
    }
}
