<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Attachment;
use App\Models\Actor;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actor_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Attachment::class)->constrained();
            $table->foreignIdFor(Actor::class)->constrained();
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
            Schema::dropIfExists('actor_attachments');
        }
    }
};
