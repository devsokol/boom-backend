<?php

use App\Enums\AuditionStatus;
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
        Schema::table('auditions', function (Blueprint $table) {
            $table
                ->unsignedTinyInteger('status')
                ->index()
                ->default(AuditionStatus::IN_REVIEW->value)
                ->after('type');
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
            Schema::table('auditions', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
