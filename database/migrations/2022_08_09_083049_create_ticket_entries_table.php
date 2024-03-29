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
        if (!Schema::hasTable('ticket_entries')) {
            Schema::create('ticket_entries', function (Blueprint $table) {
                $table->id();
                $table->integer('ticket_id');
                $table->integer('user_id');
                $table->text('content');
                $table->enum('channel', ['sms', 'web', 'voice']);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_entries');
    }
};
