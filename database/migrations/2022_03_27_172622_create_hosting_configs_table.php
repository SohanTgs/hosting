<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostingConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hosting_configs', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('hosting_id')->default(0);
            $table->unsignedInteger('configurable_group_option_id')->default(0);
            $table->unsignedInteger('configurable_group_sub_option_id')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hosting_configs');
    }
}
