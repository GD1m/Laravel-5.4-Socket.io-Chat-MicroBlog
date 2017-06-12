<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author',
        'content',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    /**
     * Get the likes for the message
     */
    public function likes()
    {
        return $this->hasMany('App\Like');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachmentImages()
    {
        return $this->hasMany('App\AttachmentImage');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachmentLinks()
    {
        return $this->hasMany('App\AttachmentLink');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attachmentYoutubes()
    {
        return $this->hasMany('App\AttachmentYoutube');
    }

    /**
     *
     */
    public function like()
    {
        $this->likeByUser(Auth::user());
    }

    /**
     * @param User $user
     */
    public function likeByUser(User $user)
    {
        Like::create([
            'message_id' => $this->id,
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
    }

    /**
     *
     */
    public function deleteImages()
    {
        $images = $this->attachmentImages()->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image);
        }
    }
}
