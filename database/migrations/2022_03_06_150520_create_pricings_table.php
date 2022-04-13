<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();

            $table->string('type');
            $table->unsignedInteger('configurable_group_sub_option_id');

            $table->decimal('monthly_setup_fee', 28, 8);
            $table->decimal('quarterly_setup_fee', 28, 8);
            $table->decimal('semi_annually_setup_fee', 28, 8);
            $table->decimal('annually_setup_fee', 28, 8);
            $table->decimal('biennially_setup_fee', 28, 8);
            $table->decimal('triennially_setup_fee', 28, 8);

            $table->decimal('monthly', 28, 8);
            $table->decimal('quarterly', 28, 8);
            $table->decimal('semi_annually', 28, 8);
            $table->decimal('annually', 28, 8);
            $table->decimal('biennially', 28, 8);
            $table->decimal('triennially', 28, 8);

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
        Schema::dropIfExists('pricings');
    }
}
