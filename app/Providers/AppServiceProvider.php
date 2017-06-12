<?php

namespace App\Providers;

use App\AttachmentImage;
use App\Events\LikeCreated;
use App\Events\MessageDeleted;
use App\Like;
use App\Message;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Message::deleting(function ($message) {
            $message->deleteImages();
        });

        Message::deleted(function ($message) {
            event(new MessageDeleted($message->id));
        });

        Like::created(function ($like) {
            event(new LikeCreated($like));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
