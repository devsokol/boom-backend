<?php

use App\Models\Role;
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
        Schema::create('user_viewed_application', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Role::class)->constrained();

            $table->unsignedInteger('amount_viewed_applications')->default(0);
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
            Schema::dropIfExists('user_viewed_application');
        }
    }
};
