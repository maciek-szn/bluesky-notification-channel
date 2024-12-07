<?php

namespace NotificationChannels\Bluesky\IdentityRepository;

use Illuminate\Cache\Repository as CacheRepository;
use NotificationChannels\Bluesky\BlueskyIdentity;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyIdentityFound;

class IdentityRepositoryUsingCache implements IdentityRepository
{
    public const DEFAULT_CACHE_KEY = 'bluesky-notification-channel:identity';

    public function __construct(
        private readonly CacheRepository $cache,
        private readonly string $key,
    ) {
    }

    public function clearIdentity(string $username): void
    {
        $this->cache->forget($this->key . ':' . md5($username));
    }

    public function hasIdentity(string $username): bool
    {
        return $this->cache->get($this->key . ':' . md5($username)) instanceof BlueskyIdentity;
    }

    public function getIdentity(string $username, string $password): BlueskyIdentity
    {
        if (!$this->hasIdentity($username)) {
            throw NoBlueskyIdentityFound::create();
        }

        return $this->cache->get($this->key . ':' . md5($username));
    }

    public function setIdentity(BlueskyIdentity $identity): void
    {
        $this->cache->set(
            key: $this->key . ':' . md5($identity->handle),
            value: $identity,
        );
    }
}
