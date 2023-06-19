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
        Schema::create('actor_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('allow_app_notification')->default(true);
            $table->boolean('role_approve_notification')->default(true);
            $table->boolean('role_reject_notification')->default(true);
            $table->boolean('role_offer_notification')->default(true);
            $table->boolean('audition_notification')->default(true);
            $table->boolean('selftape_notification')->default(true);
            $table->timestamps();

            $table->foreignIdFor(Actor::class)
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
            Schema::dropIfExists('actor_settings');
        }
    }
};
