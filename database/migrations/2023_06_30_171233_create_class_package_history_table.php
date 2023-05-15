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
        Schema::create('class_package_history', function (Blueprint $table) {
            $table->id();
            $table->string('no_class_package_history');
            $table->foreignId('id_class_booking')->nullable()->constrained('class_booking')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('date_time')->nullable();
            $table->integer('sisa_deposit_kelas')->nullable();
            $table->date('expired_date')->nullable();
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
        Schema::dropIfExists('class_package_history');
    }
};
