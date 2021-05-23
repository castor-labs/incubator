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

namespace Castor\Fiber;

/**
 * Class StatefulContext.
 */
final class StatefulContext extends DecoratedContext
{
    public const ATTR = 'session';

    /**
     * StatefulContext constructor.
     */
    protected function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public static function initialize(Context $context, Session $session): StatefulContext
    {
        $context->getRequest()->getContext()->put(self::ATTR, $session);

        return new self($context);
    }

    public function getSession(): Session
    {
        $session = $this->context->getRequest()->getContext()->get(self::ATTR);
        if (!$session instanceof Session) {
            throw new \RuntimeException('There was a problem getting the session. Maybe the context key has been overridden.');
        }

        return $session;
    }
}
