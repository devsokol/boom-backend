<?php

use App\Models\Actor;
use App\Models\PersonalSkill;
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
        Schema::create('actor_personal_skill', function (Blueprint $table) {
            $table->foreignIdFor(Actor::class)->constrained();
            $table->foreignIdFor(PersonalSkill::class)->constrained();
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
            Schema::dropIfExists('actor_personal_skill');
        }
    }
};
