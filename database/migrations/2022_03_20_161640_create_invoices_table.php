<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('deposit_id')->default(0);
            $table->unsignedInteger('user_id')->default(0);

            $table->date('date')->nullable(true);
            $table->date('due_date')->nullable(true);
            $table->date('paid_date')->nullable(true);
            $table->date('last_capture_attempt')->nullable(true);

            $table->decimal('amount', 28, 18)->default(0);
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
        Schema::dropIfExists('invoices');
    }
}
