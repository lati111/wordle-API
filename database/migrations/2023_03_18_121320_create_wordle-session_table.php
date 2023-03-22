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
        Schema::create('wordle_session', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->integer('score')->default(0);
            $table->integer('words')->default(10);
            $table->string('status', 18);
            $table->foreignUuid('user');
            $table->foreignUuid('client');
            $table->timestamps();

            $table->foreign('client')->references('uuid')->on('client')->onDelete('cascade')->onUpdate('no action');
            $table->foreign('user')->references('uuid')->on('users')->onDelete('cascade')->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wordle_session');
    }
};
