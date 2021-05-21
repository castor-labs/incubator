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

use Brick\DateTime\Instant;

/**
 * Trait SessionSerializer.
 */
trait SessionSerializer
{
    public function deserialize(Context $context, SessionConfig $config, string $data): Session
    {
        $array = unserialize($data, ['allowed_classes' => false]);

        return new Session(
            $context,
            $config,
            $array['_id'],
            $array['_data'],
            Instant::of($array['_created'])
        );
    }

    private function serialize(Session $session): string
    {
        $data = [
            '_id' => $session->getId(),
            '_data' => $session->all(),
            '_created' => $session->getCreatedAt()->getEpochSecond(),
        ];

        return serialize($data);
    }
}
