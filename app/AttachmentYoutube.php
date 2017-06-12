<?php

namespace App;


class AttachmentYoutube extends AttachmentAbstract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'video_id',
        'start_time',
    ];

    /**
     * @var Message
     */
    protected $message;

    /**
     * @param array $attributes
     */
    public function create(array $attributes = [])
    {
        $this->fill([
            'video_id' => $attributes['value'],
            'start_time' => $attributes['startTime'],
        ]);

        $this->save();
    }
}
