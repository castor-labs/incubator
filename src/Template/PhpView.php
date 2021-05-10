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

namespace Castor\Template;

use Castor\Io;

/**
 * Class PhpView.
 */
final class PhpView implements View
{
    private string $filename;
    private array $context;
    private ?Io\Buffer $buffer;

    /**
     * PhpView constructor.
     */
    public function __construct(string $filename, array $context)
    {
        $this->filename = $filename;
        $this->context = $context;
        $this->buffer = null;
    }

    public function read(string &$bytes, int $length = self::DEFAULT_READ_SIZE): int
    {
        return $this->getBuffer()->read($bytes, $length);
    }

    /**
     * @throws Io\Error
     */
    public function writeTo(Io\Writer $writer): int
    {
        return $this->getBuffer()->writeTo($writer);
    }

    private function render(): string
    {
        extract($this->context, EXTR_OVERWRITE);
        ob_start();

        include $this->filename;

        return ob_get_clean();
    }

    /**
     * @throws Io\Error
     */
    private function getBuffer(): Io\Buffer
    {
        if (null === $this->buffer) {
            $this->buffer = Io\Buffer::from($this->render());
            $this->buffer->seek(0, Io\Seeker::START);
        }

        return $this->buffer;
    }
}
