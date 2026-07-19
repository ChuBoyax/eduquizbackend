<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaderboardUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Room $room)
    {
    }

    public function broadcastOn(): Channel
    {
        // Public channel — walang auth para makinig ang teacher at students
        return new Channel('room.' . $this->room->code);
    }

    public function broadcastAs(): string
    {
        return 'leaderboard.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->room->status,
            'leaderboard' => $this->room->buildLeaderboard(),
        ];
    }
}
