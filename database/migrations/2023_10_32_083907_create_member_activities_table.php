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
        Schema::create('member_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_member')->nullable()->constrained('member')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('date_time')->nullable();
            $table->string('name_activity')->nullable();
            $table->string('no_activity')->nullable();
            $table->string('price_activity')->nullable();
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
        Schema::dropIfExists('member_activities');
    }
};
