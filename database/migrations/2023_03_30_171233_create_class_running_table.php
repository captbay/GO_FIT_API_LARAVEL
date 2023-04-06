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
            $table->foreignId('id_instruktur')->constrained('instruktur')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_class_detail')->constrained('class_detail')->onUpdate('cascade')->onDelete('cascade');
            $table->time('start_class');
            $table->time('end_class');
            $table->integer('capacity')->nullable();
            $table->date('date');
            $table->boolean('status')->nullable();
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
