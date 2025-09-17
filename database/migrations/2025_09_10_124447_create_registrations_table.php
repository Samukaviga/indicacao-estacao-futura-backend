<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('student_name', 150)->nullable();
            $table->string('responsible_name', 150)->nullable();

            $table->string('indicated_student_name', 150)->nullable();
            $table->string('indicated_responsible_name', 150)->nullable();
            $table->string('indicated_mobile_phone', 20)->nullable();
            $table->string('indicated_email', 120)->nullable();
            $table->string('indicated_education_level', 60)->nullable();
            $table->date('indicated_date_of_birth')->nullable();

            $table->string('lead_source')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('gclid')->nullable();
            $table->string('fbclid')->nullable();
            $table->string('msclkid')->nullable();
            $table->string('referrer')->nullable();
            $table->string('landing_page')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
