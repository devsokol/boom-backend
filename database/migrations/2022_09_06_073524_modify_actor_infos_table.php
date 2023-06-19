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
        Schema::table('actor_infos', function (Blueprint $table) {
            $table->after('bio', function (Blueprint $table) {
                $table->unsignedTinyInteger('acting_gender')->index()->nullable();
                $table->unsignedTinyInteger('min_age')->nullable()->default(0);
                $table->unsignedTinyInteger('max_age')->nullable()->default(0);
                $table->string('pseudonym', 100)->nullable();
                $table->string('city', 100)->nullable();
            });
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
            Schema::table('actor_infos', function (Blueprint $table) {
                $table->dropColumn('acting_gender');
                $table->dropColumn('min_age');
                $table->dropColumn('max_age');
                $table->dropColumn('pseudonym');
                $table->dropColumn('city');
            });
        }
    }
};
