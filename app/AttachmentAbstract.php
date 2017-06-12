<?php

namespace App;

use App\Extensions\Attachments\AttachmentContract;
use Illuminate\Database\Eloquent\Model;

abstract class AttachmentAbstract extends Model implements AttachmentContract
{
    /**
     * @var Message
     */
    protected $message;

    /**
     * @param array $attributes
     */
    abstract public function create(array $attributes = []);

    /**
     * @param Message $message
     * @return $this
     */
    public function setMessageId(Message $message)
    {
        $this->message = $message;

        $this->setAttribute('message_id', $message->id);

        return $this;
    }

    /**
     * Get the message that owns the attachment
     */
    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}
