<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreForumThreadViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_forum__thread_views', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('thread_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            // Thread foreign.
            $databaseTable = 'netcore_forum__threads';
            $table->foreign('thread_id')->references('id')->on($databaseTable)->onDelete('CASCADE');

            // User foreign.
            $databaseTable = config('netcore.module-admin.user.table', 'users');
            $table->foreign('user_id')->references('id')->on($databaseTable)->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netcore_forum__thread_views');
    }
}
