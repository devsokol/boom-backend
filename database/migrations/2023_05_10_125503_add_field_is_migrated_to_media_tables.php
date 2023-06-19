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
        Schema::table('selftapes', function (Blueprint $table) {
            $table->boolean('is_migrate')->default(false);
        });

        Schema::table('headshots', function (Blueprint $table) {
            $table->boolean('is_migrate')->default(false);
        });

        Schema::table('application_selftape_materials', function (Blueprint $table) {
            $table->boolean('is_migrate')->default(false);
        });

        Schema::table('role_materials', function (Blueprint $table) {
            $table->boolean('is_migrate')->default(false);
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
            Schema::table('selftapes', function (Blueprint $table) {
                $table->dropColumn('is_migrate');
            });

            Schema::table('headshots', function (Blueprint $table) {
                $table->dropColumn('is_migrate');
            });

            Schema::table('application_selftape_materials', function (Blueprint $table) {
                $table->dropColumn('is_migrate');
            });

            Schema::table('role_materials', function (Blueprint $table) {
                $table->dropColumn('is_migrate');
            });
        }
    }
};
