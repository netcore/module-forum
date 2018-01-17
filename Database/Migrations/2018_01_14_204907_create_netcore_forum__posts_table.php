<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;

class CreateNetcoreForumPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_forum__posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('thread_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('is_first')->default(false);

            /** @see https://github.com/lazychaser/laravel-nestedset#the-schema */
            NestedSet::columns($table);

            $table->text('content');
            $table->timestamps();
            $table->softDeletes();

            // Thread foreign.
            $table->foreign('thread_id')->references('id')->on('netcore_forum__threads')->onDelete('CASCADE');

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
        Schema::dropIfExists('netcore_forum__posts');
    }
}
