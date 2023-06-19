<?php

use App\Models\Actor;
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
        Schema::create('headshots', function (Blueprint $table) {
            $table->id();
            $table->string('headshot');
            $table->timestamps();

            $table->foreignIdFor(Actor::class)->constrained();
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
            Schema::dropIfExists('headshots');
        }
    }
};
