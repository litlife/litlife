<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ManagersDeleteUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->dropUnique(['manageable_type', 'manageable_id', 'user_id', 'deleted_at']);
            $table->index(['manageable_type', 'manageable_id', 'user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('managers', function (Blueprint $table) {
            $table->dropIndex(['manageable_type', 'manageable_id', 'user_id', 'deleted_at']);
            $table->unique(['manageable_type', 'manageable_id', 'user_id', 'deleted_at']);
        });
    }
}
