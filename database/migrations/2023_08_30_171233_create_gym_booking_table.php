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
        Schema::create('gym_booking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_gym')->constrained('gym')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_member')->constrained('member')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('date_time');
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
        Schema::dropIfExists('gym_booking');
    }
};