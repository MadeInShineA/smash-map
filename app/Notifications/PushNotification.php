<?php

namespace App\Notifications;

use App\Enums\GameEnum;
use App\Enums\NotificationTypeEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class PushNotification extends Notification
{
    use Queueable;

    private \App\Models\Notification $notification;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [PusherChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toPushNotification($notifiable)
    {
        return PusherMessage::create()
            ->web()
            ->title(NotificationTypeEnum::LABELS[$this->notification->type] . " for " . GameEnum::GAMES[$this->notification->game_id])
            ->body(strip_tags($this->notification->message))
            ->icon($this->notification->image_url)
            ->link(\Illuminate\Support\Facades\URL::to('/notifications'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
