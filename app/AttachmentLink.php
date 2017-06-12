<?php

namespace App;


class AttachmentLink extends AttachmentAbstract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'href',
        'title',
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
            'href' => $attributes['value'],
            'title' => $attributes['title'],
        ]);

        $this->save();
    }
}
