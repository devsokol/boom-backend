<?php

use App\Models\ApplicationSelftape;
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
        Schema::create('application_selftape_materials', function (Blueprint $table) {
            $table->id();
            $table->string('attachment')->nullable();
            $table->foreignIdFor(MaterialType::class)->constrained();
            $table->foreignIdFor(ApplicationSelftape::class)->constrained();
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
            Schema::dropIfExists('application_selftape_materials');
        }
    }
};
