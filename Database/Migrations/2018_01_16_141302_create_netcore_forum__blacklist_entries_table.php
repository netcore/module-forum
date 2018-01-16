<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreForumBlacklistEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_forum__blacklist_entries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('thread_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // User foreign.
            $databaseTable = app(config('netcore.module-forum.user.model'))->getTable();
            $table->foreign('user_id')->references('id')->on($databaseTable)->onDelete('CASCADE');

            // Category foreign.
            $databaseTable = app(\Modules\Category\Models\Category::class)->getTable();
            $table->foreign('category_id')->references('id')->on($databaseTable)->onDelete('CASCADE');

            // Thread foreign.
            $databaseTable = app(\Modules\Forum\Models\Thread::class)->getTable();
            $table->foreign('thread_id')->references('id')->on($databaseTable)->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_forum__blacklist_entries');
    }
}
