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
        Schema::table('roles', function (Blueprint $table) {
            $table->date('application_deadline')->nullable()->change();

            $table->unsignedBigInteger('country_id')->nullable()->change();
            $table->unsignedBigInteger('ethnicity_id')->nullable()->change();
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
            Schema::table('roles', function (Blueprint $table) {
                $table->date('application_deadline')->change();

                $table->unsignedBigInteger('country_id')->change();
                $table->unsignedBigInteger('ethnicity_id')->change();
            });
        }
    }
};
