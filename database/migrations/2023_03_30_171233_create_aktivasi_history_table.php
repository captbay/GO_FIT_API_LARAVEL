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
        Schema::create('aktivasi_history', function (Blueprint $table) {
            $table->id();
            $table->string('no_aktivasi_history');
            $table->foreignId('id_member')->nullable()->constrained('member')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('id_pegawai')->nullable()->constrained('pegawai')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('date_time');
            $table->double('price');
            $table->date('expired_date');
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
        Schema::dropIfExists('aktivasi_history');
    }
};