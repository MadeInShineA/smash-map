<?php

namespace App\Events;

use App\Http\Resources\Notification\NotificationResource;
use App\Models\Notification;
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

    private Notification $notification;
    private User $user;


    public function __construct(Notification $notification, User $user)
    {
        $this->notification = $notification;
        $this->user = $user;

    }

    public function broadcastWith()
    {
        $resource = new NotificationResource($this->notification);
        return json_decode($resource->toJson(), true);
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
