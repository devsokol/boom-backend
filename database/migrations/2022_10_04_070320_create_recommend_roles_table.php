<?php

use App\Enums\RecommendRoleStatus;
use App\Models\Application;
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
        Schema::create('recommend_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->index()->default(RecommendRoleStatus::IN_REVIEW->value);

            $table->foreignIdFor(Role::class)->constrained();
            $table->foreignIdFor(Application::class)->constrained();
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
            Schema::dropIfExists('recommend_roles');
        }
    }
};
