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

namespace Castor\Fiber;

use Castor\Fiber\Extension\TemplateEngine;
use Castor\Template;

/**
 * Class TemplateSupport.
 */
final class TemplateSupport implements Middleware
{
    private Template\Engine $engine;

    /**
     * TemplateSupport constructor.
     */
    public function __construct(Template\Engine $engine)
    {
        $this->engine = $engine;
    }

    public function process(Context $ctx, Stack $stack): void
    {
        $ctx = new TemplateEngine($ctx, $this->engine);
        $stack->next()->handle($ctx);
    }
}
