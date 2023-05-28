<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_incomes', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->nullable();
            $table->string('bulan')->nullable();
            $table->double('aktivasi')->nullable();
            $table->double('deposit')->nullable();
            $table->double('total')->nullable();
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
        Schema::dropIfExists('report_incomes');
    }
};
