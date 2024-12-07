<?php

namespace NotificationChannels\Bluesky;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use NotificationChannels\Bluesky\Exceptions\NoBlueskyChannel;
use NotificationChannels\Bluesky\IdentityRepository\IdentityRepository;

final class BlueskyChannel
{
    public function __construct(
        protected readonly BlueskyService $bluesky,
        protected readonly BlueskyClient $client,
        protected readonly IdentityRepository $identityRepository,
        protected readonly SessionManager $sessionManager,
        protected readonly ConfigRepository $config,
    ) {
    }

    public function send(mixed $notifiable, Notification $notification): string
    {
        if (!method_exists($notification, 'toBluesky')) {
            throw NoBlueskyChannel::create(\get_class($notification));
        }

        $routing = $notifiable->routeNotificationFor('bluesky');

        $username = Arr::get($routing, 'username', function () {
            return $this->config->get('services.bluesky.username');
        });

        $password = Arr::get($routing, 'password', function () {
            return $this->config->get('services.bluesky.password');
        });

        $identity = $this->sessionManager->getIdentity(
            username: $username,
            password: $password,
        );

        return $this->bluesky->createPost(
            identity: $identity,
            post: $notification->toBluesky($notifiable),
        );
    }
}
