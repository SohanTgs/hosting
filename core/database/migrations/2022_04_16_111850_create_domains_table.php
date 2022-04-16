<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('order_id')->default(0);
            $table->unsignedInteger('coupon_id')->default(0);
            $table->unsignedInteger('deposit_id')->default(0);
            $table->string('domain');
            $table->tinyInteger('id_protection')->default(0);
            $table->decimal('first_payment_amount', 28, 16)->default(0);
            $table->decimal('recurring_amount', 28, 16)->default(0);
            $table->integer('reg_period')->default(1);
            $table->dateTime('expiry_date');
            $table->dateTime('next_due_date');
            $table->dateTime('next_invoice_date');
            $table->tinyInteger('status')->comment('0=> Initiate,1=>Active, 2=>Pending')->default(0);

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
        Schema::dropIfExists('domains');
    }
}
