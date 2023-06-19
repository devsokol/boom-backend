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
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('actor_id')->nullable()->change();

            $table->dropForeign('applications_actor_id_foreign');
            $table->foreign('actor_id')->references('id')->on('actors')->cascadeOnUpdate()->nullOnDelete();
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
            Schema::table('applications', function (Blueprint $table) {
                $table->unsignedBigInteger('actor_id')->change();

                $table->dropForeign('applications_actor_id_foreign');
                $table->foreign('actor_id')->references('id')->on('actors')->restrictOnUpdate()->restrictOnDelete();
            });
        }
    }
};
