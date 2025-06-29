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
        Schema::create('project_district_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_detail_id');
            $table->foreign('project_detail_id')->references('id')->on('project_details')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('district_id');
            $table->foreign('district_id')->references('id')->on('districts')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_district_details');
    }
};
