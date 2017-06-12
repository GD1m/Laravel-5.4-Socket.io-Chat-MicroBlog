<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentLinksTable extends Migration
{
    public function up()
    {
        Schema::create('attachment_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('message_id')->unsigned()->notNull();
            $table->string('href')->notNull();
            $table->string('title')->notNull();
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
        Schema::table('attachment_links', function (Blueprint $table) {
            $table->dropForeign('attachment_links_message_id_foreign');

            $table->dropIndex(['message_id']);
        });

        Schema::dropIfExists('attachment_links');
    }
}
