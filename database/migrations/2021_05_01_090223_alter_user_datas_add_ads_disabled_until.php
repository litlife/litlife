<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserDatasAddAdsDisabledUntil extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->timestamp('ads_disabled_until')->nullable()->comment(__('user_data.ads_disabled_until'));
        });

        \Illuminate\Support\Facades\DB::table('user_datas')
            ->where('books_purchased_count', '>', 0)
            ->update(['ads_disabled_until' => now()->addYear()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_datas', function (Blueprint $table) {
            $table->dropColumn('ads_disabled_until');
        });
    }
}
