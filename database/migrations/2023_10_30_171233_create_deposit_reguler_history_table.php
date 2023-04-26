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
        Schema::create('deposit_reguler_history', function (Blueprint $table) {
            $table->id();
            $table->string('no_deposit_reguler_history');
            $table->foreignId('id_promo_cash')->nullable()->constrained('promo_cash')->onUpdate('cascade');
            $table->foreignId('id_member')->constrained('member')->onUpdate('cascade');
            $table->foreignId('id_pegawai')->constrained('pegawai')->onUpdate('cascade');
            $table->dateTime('date_time');
            $table->double('topup_amount');
            $table->double('bonus');
            $table->double('sisa');
            $table->double('total');
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
        Schema::dropIfExists('deposit_regular_history');
    }
};
