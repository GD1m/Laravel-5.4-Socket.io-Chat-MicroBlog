<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message_id',
        'user_id',
        'username',
    ];

    /**
     * Get the message that owns the like
     */
    public function message()
    {
        return $this->belongsTo('App\Message');
    }
}
