<?php

use App\Enums\ApplicationStatus;
use App\Models\Actor;
use App\Models\Role;
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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->index()->default(ApplicationStatus::IN_REVIEW->value);
            $table->timestamps();

            $table->foreignIdFor(Actor::class)->constrained();
            $table->foreignIdFor(Role::class)->constrained();
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
            Schema::dropIfExists('applications');
        }
    }
};
