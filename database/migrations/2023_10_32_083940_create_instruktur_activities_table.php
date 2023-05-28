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
        Schema::create('instruktur_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_instruktur')->nullable()->constrained('instruktur')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('date_time')->nullable();
            $table->string('name_activity')->nullable();
            $table->string('description_activity')->nullable();
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
        Schema::dropIfExists('instruktur_activities');
    }
};
