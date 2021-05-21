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
use Castor\Io;
use Castor\Os;
use Castor\Os\Path;

/**
 * Class OsTempStore.
 */
final class OsSessionStore implements SessionStore
{
    private SessionConfig $config;
    private string $path;

    /**
     * OsTempSession constructor.
     */
    public function __construct(SessionConfig $config, string $path = null)
    {
        $this->config = $config;
        $this->path = $path ?? Path\join(Os\tempPath(), 'session');
    }

    public function get(Context $ctx): Session
    {
        Os\ensureDir($this->path);
        $sessId = $ctx->getRequest()->getCookie($this->config->cookie->getName());
        if (null === $sessId) {
            return Session::create($ctx, $this->config);
        }

        $filename = Path\join($this->path, $sessId);

        try {
            $file = Os\File::open($filename);
        } catch (Os\Error $e) {
            return new Session($ctx, $this->config, $sessId, [], Instant::now());
        }
        $data = unserialize(Io\readAll($file), ['allowed_classes' => false]);

        $session = new Session(
            $ctx,
            $this->config,
            $data['_id'],
            $data['_data'],
            Instant::of($data['_created'])
        );
        if ($session->isExpired()) {
            $session->destroy();

            return Session::create($ctx, $this->config);
        }

        return $session;
    }

    /**
     * @throws Io\Error
     */
    public function save(Session $session): void
    {
        Os\ensureDir($this->path);
        $filename = Path\join($this->path, $session->getId());
        $file = Os\File::put($filename);
        $file->seek(0, Io\Seeker::START);
        $data = [
            '_id' => $session->getId(),
            '_data' => $session->all(),
            '_created' => $session->getCreatedAt()->getEpochSecond(),
        ];
        $file->write(serialize($data));
    }

    public function destroy(Session $session): void
    {
        Os\ensureDir($this->path);
        $filename = Path\join($this->path, $session->getId());
        Os\remove($filename);
    }
}
