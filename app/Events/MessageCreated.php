<?php

namespace App\Events;

use App\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Message
     */
    public $message;

    /**
     * @var
     */
    public $likes;

    /**
     * @var
     */
    public $attachment_images;
    /**
     * @var
     */
    public $attachment_links;
    /**
     * @var
     */
    public $attachment_youtubes;

    /**
     * MessageCreated constructor.
     * @param Message $message
     */
    public function __construct(Message $message)
    {
        $this->message = $message;

        $this->likes = $this->message->likes()->get();

        $this->attachment_images = $this->message->attachmentImages()->get();
        $this->attachment_links = $this->message->attachmentLinks()->get();
        $this->attachment_youtubes = $this->message->attachmentYoutubes()->get();
    }

    /**
     * @return array
     */
    public function broadcastOn()
    {
        return [
            new Channel('chat-channel'),
        ];
    }

}
