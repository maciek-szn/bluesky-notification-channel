<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;

/**
 * Ensures that the identity is always up-to-date.
 */
final class SessionManager
{
    public function __construct(
        private readonly BlueskyClient $client,
        private readonly IdentityRepository $identityRepository,
    ) {
    }

    /**
     * Gets an updated identity.
     */
    public function getIdentity(string $username, string $password): BlueskyIdentity
    {
        $this->ensureHasIdentity($username, $password);
        $this->refreshIdentity($username, $password);

        return $this->identityRepository->getIdentity($username, $password);
    }

    /**
     * Ensures an identity exists.
     */
    private function ensureHasIdentity(string $username, string $password): void
    {
        if ($this->identityRepository->hasIdentity($username)) {
            return;
        }

        $this->identityRepository->setIdentity(
            identity: $this->client->createIdentity($username, $password),
        );
    }

    /**
     * Refreshes the existing identity.
     */
    private function refreshIdentity(string $username, string $password): void
    {
        $identity = $this->client->refreshIdentity(
            identity: $this->identityRepository->getIdentity($username, $password),
        );

        $this->identityRepository->setIdentity($identity);
    }
}
