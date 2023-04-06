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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_users')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('no_pegawai');
            $table->string('name');
            $table->string('address');
            $table->string('number_phone');
            $table->date('born_date');
            $table->string('gender');
            $table->string('role');
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
        Schema::dropIfExists('pegawai');
    }
};
