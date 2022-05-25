<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_settings', function (Blueprint $table) {
            $table->id();

            $table->text('generate_invoice');
            $table->integer('invoice_send_reminder_days')->default(0);
            $table->integer('invoice_first_over_due_reminder')->default(0);
            $table->integer('invoice_second_over_due_reminder')->default(0);
            $table->integer('invoice_third_over_due_reminder')->default(0);
            $table->integer('add_late_fee_days')->default(0);
            $table->decimal('invoice_late_fee_amount', 28, 16)->default(0);
            $table->tinyInteger('invoice_late_fee_type')->default(1);

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
        Schema::dropIfExists('billing_settings');
    }
}
