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
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->morphs('codeable');
            $table->string('code', 8)->index();
            $table->string('tag', 30)->nullable()->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['code', 'tag']);
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
            Schema::dropIfExists('verification_codes');
        }
    }
};
