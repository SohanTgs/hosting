<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('invoice_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);
            $table->enum('type', ['Setup','Item','Coupon'])->default('Item')->comment('Setup => Setup with setup amount, Item => Item details with price, Coupon => Code with amount');
            $table->longText('description');
            $table->decimal('28, 8');

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
        Schema::dropIfExists('invoice_items');
    }
}
