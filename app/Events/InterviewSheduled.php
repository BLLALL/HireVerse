<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InterviewSheduled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, InteractsWithQueue;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public int $applicantId, 
        public $deadline,
        ){}
 

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('applicant.' . $this->applicantId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'interview.scheduled';
    }
    public function broadcastWith(): array
    {

        return [

            'message' => 'Your interview has been scheduled.',
            'applicant_id' => $this->applicantId,
            'deadline' => $this->deadline->diffForHumans(),
        ];
    }

    
}
