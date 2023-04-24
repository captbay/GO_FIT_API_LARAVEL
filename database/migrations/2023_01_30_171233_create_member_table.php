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
        Schema::create('member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_users')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('no_member')->nullable();
            $table->string('name')->unique();
            $table->string('address');
            $table->string('number_phone');
            $table->date('born_date');
            $table->string('gender');
            $table->float('jumlah_deposit_reguler')->nullable();
            $table->date('expired_date_membership')->nullable();
            $table->boolean('status_membership')->nullable();
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
        Schema::dropIfExists('member');
    }
};
