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
        Schema::create('listings', function (Blueprint $table) {
            $table->id('id')->unique();
            $table->timestamps();
            $table->string('title');
            $table->text('description');
            $table->string('plant_category');
            $table->string('contact_email');
            $table->string('phone_number');
            $table->string('website')->nullable();
            $table->string('hire_rate_gbp');
            $table->string('hire_rate_eur');
            $table->string('hire_rate_usd');
            $table->string('hire_rate_aud');
            $table->string('hire_rate_nzd');
            $table->string('hire_rate_zar');
            $table->jsonb('tags');
            $table->string('company_logo')->nullable();
            $table->jsonb('photo_gallery')->nullable();
            $table->jsonb('attachments')->nullable();
            $table->jsonb('social_networks')->nullable();
            $table->string('location');
            $table->string('region');
            $table->jsonb('related_listing')->nullable();
            $table->string('hire_rental')->nullable();
            $table->string('additional_1')->nullable();
            $table->string('additional_2')->nullable();
            $table->string('additional_3')->nullable();
            $table->string('additional_4')->nullable();
            $table->string('additional_5')->nullable();
            $table->string('additional_6')->nullable();
            $table->string('additional_7')->nullable();
            $table->string('additional_8')->nullable();
            $table->string('additional_9')->nullable();
            $table->string('additional_10')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
