<?php

use App\Enums\ProjectStatus;
use App\Models\Genre;
use App\Models\ProjectType;
use App\Models\User;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('placeholder')->nullable();
            $table->string('name');
            $table->text('description');
            $table->date('start_date');
            $table->date('deadline');
            $table->unsignedTinyInteger('status')->index()->default(ProjectStatus::ACTIVE->value);
            $table->timestamps();

            $table->foreignIdFor(Genre::class)->constrained();
            $table->foreignIdFor(ProjectType::class)->nullable()->constrained();
            $table->foreignIdFor(User::class)->constrained();
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
            Schema::dropIfExists('projects');
        }
    }
};
