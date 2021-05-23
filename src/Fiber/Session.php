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

use Brick\DateTime\Instant;
use Castor\Arr;
use Castor\Net\Http;

/**
 * Class BaseSession.
 */
class Session
{
    private const FLASHES_KEY = '_flashes';

    protected string $id;
    protected array $data;
    protected bool $touched;
    protected Instant $createdAt;

    protected Context $ctx;
    protected SessionConfig $config;

    /**
     * BaseSession constructor.
     */
    public function __construct(Context $ctx, SessionConfig $config, string $id, array $data, Instant $createdAt)
    {
        $this->ctx = $ctx;
        $this->config = $config;
        $this->id = $id;
        $this->data = $data;
        $this->createdAt = $createdAt;
        $this->touched = false;
        $this->processFlashes();
    }

    public static function create(Context $ctx, SessionConfig $config): Session
    {
        $session = new self($ctx, $config, $config->generateId(), [], Instant::now());
        Http\setCookie(
            $ctx->getWriter(),
            $config->cookie
                ->withValue($session->id)
                ->withMaxAge($config->ttl->toSeconds())
        );

        return $session;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param $value
     */
    public function flash(string $key, $value)
    {
        $flashes = $this->get(self::FLASHES_KEY) ?? [];
        $flashes[$key] = $value;
        $this->set(self::FLASHES_KEY, $flashes);
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->touched = true;
    }

    public function delete(string $key): void
    {
        unset($this->data[$key]);
    }

    public function save(): void
    {
        if ($this->touched) {
            $this->config->store->save($this);
        }
    }

    public function all(): array
    {
        return $this->data;
    }

    public function destroy(): void
    {
        $this->config->store->destroy($this);
        $this->touched = false;
    }

    public function regenerate(): void
    {
        $this->id = $this->config->generateId();
        $this->createdAt = Instant::now();
        $this->touched = true;
        Http\setCookie(
            $this->ctx->getWriter(),
            $this->config->cookie->withValue($this->id)
                ->withMaxAge($this->config->ttl->toSeconds())
        );
    }

    public function getCreatedAt(): Instant
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        return $this->createdAt->plus($this->config->ttl)->isPast();
    }

    private function processFlashes(): void
    {
        $flashes = $this->data[self::FLASHES_KEY] ?? [];
        if ([] === $flashes) {
            return;
        }
        $this->data[self::FLASHES_KEY] = [];
        $this->data = Arr\merge($this->data, $flashes);
        $this->touched = true;
    }
}
