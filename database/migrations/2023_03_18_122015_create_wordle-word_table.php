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
        Schema::create('wordle__word', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('word', 5);
            $table->integer('index');
            $table->foreignUuid('session');
            $table->timestamps();

            $table->foreign('session')->references('uuid')->on('wordle__session')->onDelete('cascade')->onUpdate('no action');
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
