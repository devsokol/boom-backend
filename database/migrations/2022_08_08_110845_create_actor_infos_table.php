<?php

use App\Models\Actor;
use App\Models\Ethnicity;
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
        Schema::create('actor_infos', function (Blueprint $table) {
            $table->id();
            $table->text('bio');
            $table->string('behance_link');
            $table->string('instagram_link');
            $table->string('youtube_link');
            $table->string('facebook_link');
            $table->timestamps();

            $table->foreignIdFor(Actor::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignIdFor(Ethnicity::class)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
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
            Schema::dropIfExists('actor_infos');
        }
    }
};
