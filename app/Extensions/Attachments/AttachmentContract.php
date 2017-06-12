<?php

namespace App\Extensions\Attachments;

use App\Message;

interface AttachmentContract
{
    /**
     * @param Message $message
     * @return $this
     */
    public function setMessageId(Message $message);

    /**
     * @param array $attributes
     */
    public function create(array $attributes);
}