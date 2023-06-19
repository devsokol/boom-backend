<?php

use App\Enums\AuditionType;
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
        Schema::create('auditions', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->index()->default(AuditionType::OFFLINE->value);
            $table->string('address');
            $table->dateTime('audition_datetime');

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
            Schema::dropIfExists('auditions');
        }
    }
};
