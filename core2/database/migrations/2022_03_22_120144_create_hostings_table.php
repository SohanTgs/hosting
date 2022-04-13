<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHostingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hostings', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('order_id')->default(0);
            $table->unsignedInteger('product_id')->default(0);
            $table->unsignedInteger('server_id')->default(0);
            $table->unsignedInteger('deposit_id')->default(0);

            $table->string('domain');
            $table->decimal('first_payment_amount', 28, 18)->default(0);
            $table->decimal('amount', 28, 18)->default(0);
            $table->tinyInteger('billing_cycle');

            $table->string('reg_date');
            $table->string('next_due_date');
            $table->string('next_invoice_date');
            $table->string('last_update');

            $table->string('domain_status');
            $table->string('username')->nullable(true);
            $table->string('password');

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
        Schema::dropIfExists('hostings');
    }
}
