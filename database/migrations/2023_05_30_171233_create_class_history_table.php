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
        Schema::create('class_history', function (Blueprint $table) {
            $table->id();
            $table->string('no_class_history');
            $table->foreignId('id_class_booking')->constrained('class_booking')->onUpdate('cascade');
            $table->dateTime('date_time');
            $table->double('sisa_deposit');
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
        Schema::dropIfExists('class_history');
    }
};
