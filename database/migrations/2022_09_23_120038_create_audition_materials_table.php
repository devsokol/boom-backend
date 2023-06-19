<?php

use App\Models\Audition;
use App\Models\MaterialType;
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
        Schema::create('audition_materials', function (Blueprint $table) {
            $table->id();
            $table->string('attachment')->nullable();
            $table->foreignIdFor(MaterialType::class)->constrained();
            $table->foreignIdFor(Audition::class)->constrained();
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
            Schema::dropIfExists('audition_materials');
        }
    }
};
