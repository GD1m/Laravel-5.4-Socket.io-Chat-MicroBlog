<?php

namespace App\Extensions\Attachments;

use App\Events\MessageCreated;
use App\Exceptions\InvalidArgumentException;
use App\Message;
use Illuminate\Validation\ValidationException;

class AttachmentFactory
{
    /**
     * @param string $type
     * @param Message $message
     * @return AttachmentContract
     * @throws InvalidArgumentException
     * @internal param mixed $params
     */
    public static function build(Message $message, string $type): AttachmentContract
    {
        $attachmentClass = studly_case("App\Attachment_" . $type);

        if (class_exists($attachmentClass)) {
            return (new $attachmentClass())->setMessageId($message);
        } else {
            throw new InvalidArgumentException("Invalid attachment type given ($type). Class $attachmentClass not found.");
        }
    }

    /**
     * @param Message $message
     * @param array $attachments
     */
    public static function buildAttachments(Message $message, array $attachments)
    {
        foreach ($attachments as $attachmentType => $attachmentsArray) {
            foreach ($attachmentsArray as $attachment) {
                try {
                    $newAttachment = self::build($message, $attachmentType);

                    $newAttachment->create($attachment);
                } catch (ValidationException $e) {
                    dd($e);
                }
            }
        }
    }
}