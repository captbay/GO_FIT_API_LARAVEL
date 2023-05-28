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
        Schema::create('instruktur_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_instruktur')->constrained('instruktur')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_instruktur_pengganti')->constrained('instruktur')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_class_running')->constrained('class_running')->onUpdate('cascade')->onDelete('cascade');
            $table->text('alasan')->nullable();
            $table->boolean('is_confirm')->nullable();
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
        Schema::dropIfExists('instruktur_izin');
    }
};
