<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDomainPricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domain_pricings', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('domain_id')->default(0);

            $table->decimal('one_year_price', 28, 16)->default(0);
            $table->decimal('one_year_whois_protection', 28, 16)->default(0);

            $table->decimal('two_year_price', 28, 16)->default(0);
            $table->decimal('two_year_whois_protection', 28, 16)->default(0);

            $table->decimal('three_year_price', 28, 16)->default(0);
            $table->decimal('three_year_whois_protection', 28, 16)->default(0);
            
            $table->decimal('four_year_price', 28, 16)->default(0);
            $table->decimal('four_year_whois_protection', 28, 16)->default(0);

            $table->decimal('five_year_price', 28, 16)->default(0);
            $table->decimal('five_year_whois_protection', 28, 16)->default(0);

            $table->decimal('six_year_price', 28, 16)->default(0);
            $table->decimal('six_year_whois_protection', 28, 16)->default(0); 

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
        Schema::dropIfExists('domain_pricings');
    }
}
