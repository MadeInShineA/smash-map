<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    private User $user;
    private string $notification_type;
    private string $game_image;
    private string $message;
    private string $game_name;


    public function __construct(User $user, string $notification_type, string $game_image, string $game_name, string $message)
    {
        $this->user = $user;
        $this->notification_type = $notification_type;
        $this->game_image = $game_image;
        $this->game_name = $game_name;
        $this->message = $message;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'notificationType' => $this->notification_type,
            'gameImage' => $this->game_image,
            'gameName' => $this->game_name,

        ];
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notifications.' . $this->user->id),
        ];
    }
}
