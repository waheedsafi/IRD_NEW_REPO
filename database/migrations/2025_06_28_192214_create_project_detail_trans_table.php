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
        Schema::create('project_detail_trans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_detail_id');
            $table->foreign('project_detail_id')->references('id')->on('project_details')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->json('health_center');
            $table->string('address');
            $table->json('health_worker');
            $table->json('managment_worker');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')
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
        Schema::dropIfExists('project_detail_trans');
    }
};
