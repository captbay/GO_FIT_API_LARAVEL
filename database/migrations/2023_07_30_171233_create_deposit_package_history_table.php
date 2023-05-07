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
        Schema::create('deposit_package_history', function (Blueprint $table) {
            $table->id();
            $table->string('no_deposit_package_history');
            $table->foreignId('id_promo_class')->nullable()->constrained('promo_class')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('id_class_detail')->constrained('class_detail')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('id_member')->constrained('member')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('id_pegawai')->constrained('pegawai')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('date_time');
            $table->double('total_price');
            $table->integer('package_amount');
            $table->date('expired_date')->nullable();
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
        Schema::dropIfExists('deposit_package_history');
    }
};