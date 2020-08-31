<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmilesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('smiles')) {
			Schema::create('smiles', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('name')->unique();
				$table->string('description')->unique();
				$table->string('simple_form')->nullable()->unique();
				$table->string('for')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->text('parameters')->nullable();
				$table->string('storage', 30)->default('public');
				$table->string('dirname')->nullable();
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('smiles');
	}

}
