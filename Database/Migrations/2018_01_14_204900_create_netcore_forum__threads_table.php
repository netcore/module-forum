<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetcoreForumThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netcore_forum__threads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->unsignedInteger('replies')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->timestamps();
            $table->softDeletes();

            // Category foreign.
            $databaseTable = app(\Modules\Category\Models\Category::class)->getTable();
            $table->foreign('category_id')->references('id')->on($databaseTable)->onDelete('CASCADE');

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
        Schema::dropIfExists('netcore_forum__threads');
    }
}
