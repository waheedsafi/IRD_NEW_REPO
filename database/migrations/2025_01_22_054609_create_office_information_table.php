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
        Schema::create('office_information', function (Blueprint $table) {
            $table->id();
            $table->string('address_en',512);
            $table->string('address_fa',512);
            $table->string('address_ps',512);
            $table->string('contact',32);
            $table->string('email',64);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_information');
    }
};
