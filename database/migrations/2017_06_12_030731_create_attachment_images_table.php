<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachment_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_id')->unsigned()->notNull();
            $table->string('image')->notNull();
            $table->timestamps();

            $table->index('message_id');

            $table->foreign('message_id')
                ->references('id')->on('messages')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachment_images', function (Blueprint $table) {
            $table->dropForeign('attachment_images_message_id_foreign');

            $table->dropIndex(['message_id']);
        });

        Schema::dropIfExists('attachment_images');
    }
}
