<?php

namespace NotificationChannels\Bluesky;

use NotificationChannels\Bluesky\Embeds\Embed;
use NotificationChannels\Bluesky\Embeds\EmbedResolver;
use NotificationChannels\Bluesky\Facets\FacetsResolver;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;

final class BlueskyService
{
    public function __construct(
        protected readonly BlueskyClient $client,
        protected readonly IdentityRepository $identityRepository,
        protected readonly SessionManager $sessionManager,
        protected readonly EmbedResolver $embedResolver,
        protected readonly FacetsResolver $facetsResolver,
    ) {
    }

    public function createPost(BlueskyIdentity $identity, BlueskyPost|string $post): string
    {
        return $this->client->createPost(
            identity: $identity,
            post: $this->resolvePost($identity, $post),
        );
    }

    public function uploadBlob(BlueskyIdentity $identity, string $pathOrUrl): Blob
    {
        return $this->client->uploadBlob(
            identity: $identity,
            pathOrUrl: $pathOrUrl,
        );
    }

    public function resolvePost(BlueskyIdentity $identity, string|BlueskyPost $post): BlueskyPost
    {
        if (\is_string($post)) {
            $post = BlueskyPost::make()->text($post);
        }

        if ($post->automaticallyResolvesFacets()) {
            $post->facets(facets: $this->facetsResolver->resolve($this, $post));
        }

        if ($embed = $this->resolveEmbed($identity, $post)) {
            $post->embed(embed: $embed);
        }

        return $post;
    }

    private function resolveEmbed(BlueskyIdentity $identity, BlueskyPost $post): ?Embed
    {
        if ($post->embedUrl) {
            return $this->embedResolver->createEmbedFromUrl($this, $identity, $post->embedUrl);
        }

        if ($post->automaticallyResolvesEmbeds()) {
            return $this->embedResolver->resolve($this, $identity, $post);
        }

        return null;
    }

    public function getClient(): BlueskyClient
    {
        return $this->client;
    }
}
