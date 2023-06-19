<?php

use App\Enums\Gender;
use App\Enums\RoleStatus;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Ethnicity;
use App\Models\PaymentType;
use App\Models\Project;
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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->unsignedInteger('rate');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->unsignedTinyInteger('status')->index()->default(RoleStatus::PRIVATE->value);
            $table->unsignedTinyInteger('acting_gender')->index()->default(Gender::MALE->value);
            $table->unsignedTinyInteger('min_age');
            $table->unsignedTinyInteger('max_age');
            $table->date('application_deadline')->index();
            $table->timestamps();

            $table->foreignIdFor(Country::class)->constrained();
            $table->foreignIdFor(Project::class)->constrained();
            $table->foreignIdFor(Currency::class)->constrained();
            $table->foreignIdFor(PaymentType::class)->constrained();
            $table->foreignIdFor(Ethnicity::class)->constrained();
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
            Schema::dropIfExists('roles');
        }
    }
};
