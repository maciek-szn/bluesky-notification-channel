<?php

namespace NotificationChannels\Bluesky\IdentityRepository;

use NotificationChannels\Bluesky\BlueskyIdentity;

interface IdentityRepository
{
    /**
     * Determines whether an identity is stored.
     */
    public function hasIdentity(string $username): bool;

    /**
     * Gets the identity.
     */
    public function getIdentity(string $username, string $password): BlueskyIdentity;

    /**
     * Saves the identity.
     */
    public function setIdentity(BlueskyIdentity $identity): void;

    /**
     * Clears the identity.
     */
    public function clearIdentity(string $username): void;
}
