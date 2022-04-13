<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration 
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('deposit_id');
            $table->unsignedInteger('invoice_id'); 
            $table->unsignedInteger('user_id');

            $table->text('name_servers');
            $table->text('renewals');

            $table->decimal('amount', 28, 18)->default(0);
            $table->text('ip_address');
            $table->text('order_data');
            $table->text('notes')->nullable(true); 
            $table->tinyInteger('status');

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
        Schema::dropIfExists('orders');
    }
}
