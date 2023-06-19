<?php

use App\Enums\ApplicationSelftapeStatus;
use App\Models\Application;
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
        Schema::create('application_selftapes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->index()->default(ApplicationSelftapeStatus::IN_REVIEW->value);
            $table->string('description');
            $table->dateTime('deadline_datetime');

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
            Schema::dropIfExists('application_selftapes');
        }
    }
};
