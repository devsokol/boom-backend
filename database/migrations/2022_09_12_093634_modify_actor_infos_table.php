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
            $table->text('bio')->nullable()->change()->after('id');

            $table->after('bio', function (Blueprint $table) {
                $table->string('behance_link')->nullable()->change();
                $table->string('instagram_link')->nullable()->change();
                $table->string('youtube_link')->nullable()->change();
                $table->string('facebook_link')->nullable()->change();
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
                $table->text('bio')->change()->after('id');

                $table->after('bio', function (Blueprint $table) {
                    $table->string('behance_link')->change();
                    $table->string('instagram_link')->change();
                    $table->string('youtube_link')->change();
                    $table->string('facebook_link')->change();
                });
            });
        }
    }
};
