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

            \Illuminate\Support\Facades\DB::statement('drop index if exists managers_manageable_type_manageable_id_user_id_deleted_at_uniqu;');
            //$table->dropUnique(['manageable_type', 'manageable_id', 'user_id', 'deleted_at']);

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
            $table->unique(['manageable_type', 'manageable_id', 'user_id', 'deleted_at'], 'managers_manageable_type_manageable_id_user_id_deleted_at_uniqu');
        });
    }
}
