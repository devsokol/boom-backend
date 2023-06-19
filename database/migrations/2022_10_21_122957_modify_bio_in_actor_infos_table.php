<?php

use App\Models\ActorInfo;
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
        $this->trimLargeBio();

        Schema::table('actor_infos', function (Blueprint $table) {
            $table->string('bio')->change();
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
            Schema::table('actor_infos', function (Blueprint $table) {
                $table->text('bio')->change();
            });
        }
    }

    private function trimLargeBio(): void
    {
        ActorInfo::select('id', 'bio')->each(function ($actorInfo) {
            if (str($actorInfo->bio)->length > 255) {
                $actorInfo->update(['bio' => str($actorInfo->bio)->limit(252)]);
            }
        });
    }
};
