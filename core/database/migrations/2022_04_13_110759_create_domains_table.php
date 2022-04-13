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

            $table->string('extension'); 
            $table->tinyInteger('dns_management'); 
            $table->tinyInteger('email_forwarding'); 
            $table->tinyInteger('id_protection'); 
            $table->tinyInteger('epp_code'); 
            $table->integer('order'); 
            $table->boolean('status')->default(1); 

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
