<?php

namespace NotificationChannels\Bluesky\Embeds;

use NotificationChannels\Bluesky\BlueskyIdentity;
use NotificationChannels\Bluesky\BlueskyPost;
use NotificationChannels\Bluesky\BlueskyService;

interface EmbedResolver
{
    /**
     * Resolves an embed from the given post.
     */
    public function resolve(BlueskyService $bluesky, BlueskyIdentity $identity, BlueskyPost $post): ?Embed;

    /**
     * Create an embed from the given URL.
     */
    public function createEmbedFromUrl(BlueskyService $bluesky, BlueskyIdentity $identity, string $url): ?Embed;
}
