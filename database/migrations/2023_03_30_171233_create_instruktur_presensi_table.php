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
        Schema::create('instruktur_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_instruktur')->constrained('instruktur')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('status_class');
            $table->time('start_class');
            $table->time('end_class');
            $table->date('date');
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
        Schema::dropIfExists('instruktur_presensi');
    }
};
