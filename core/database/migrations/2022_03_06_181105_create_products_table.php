<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('category_id');
            $table->tinyInteger('product_type');
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->tinyInteger('welcome_message');
            $table->boolean('domain_register');
 
            $table->boolean('stock_control');
            $table->integer('stock_quantity');

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
        Schema::dropIfExists('products');
    }
}
