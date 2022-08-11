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
        if (Schema::hasColumn('tickets', 'notification_method')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('notification_method');
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
        if (!Schema::hasColumn('tickets', 'notification_method')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->enum('notification_method', ['sms', 'voice']);
            });
        }
    }
};
