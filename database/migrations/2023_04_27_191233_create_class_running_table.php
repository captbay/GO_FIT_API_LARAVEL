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
        Schema::create('class_running', function (Blueprint $table) {
            $table->id();
            //masih bingung 
            // $table->foreignId('id_instruktur')->constrained('instruktur')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_jadwal_umum')->constrained('jadwal_umum')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('capacity');
            $table->date('date');
            $table->string('day_name');
            $table->string('status');
            // $table->string('nama_instruktur_pengganti')->nullable();
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
        Schema::dropIfExists('class');
    }
};