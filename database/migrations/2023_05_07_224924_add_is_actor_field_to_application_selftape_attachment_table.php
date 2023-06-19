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
        Schema::table('application_selftape_attachment', function (Blueprint $table) {
            $table->boolean('is_actor')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (app()->isLocal()) {
            Schema::table('application_selftape_attachment', function (Blueprint $table) {
                $table->dropColumn('is_actor');
            });
        }
    }
};
