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

namespace Castor\Storage;

use Castor\Net\Uri;

/**
 * Class StorageRegistry.
 */
final class CompositeFactory implements Factory
{
    /**
     * @var Factory[]
     */
    private array $factories;

    /**
     * CompositeFactory constructor.
     *
     * @param Factory ...$factories
     */
    public function __construct(Factory ...$factories)
    {
        $this->factories = $factories;
    }

    public function register(Factory $factory): void
    {
        $this->factories[] = $factory;
    }

    /**
     * @throws UnsupportedScheme
     */
    public function create(Uri $uri): Driver
    {
        foreach ($this->factories as $factory) {
            try {
                return $factory->create($uri);
            } catch (UnsupportedScheme $e) {
                continue;
            }
        }

        throw new UnsupportedScheme('No factories could create a driver for scheme '.$uri->getScheme(), 0);
    }
}
