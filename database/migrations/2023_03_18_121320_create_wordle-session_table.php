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
        Schema::create('wordle__session', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->integer('words')->default(10);
            $table->integer('wordIndex')->default(1);
            $table->string('status', 18);
            $table->foreignUuid('user');
            $table->timestamps();

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
        //
    }
};
