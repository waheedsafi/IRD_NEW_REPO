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
        Schema::create('model_job_trans', function (Blueprint $table) {
            $table->id();
            $table->string('value', 128);
            $table->unsignedBigInteger('model_job_id');
            $table->foreign('model_job_id')->references('id')->on('model_jobs')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
            $table->index(["language_name", "model_job_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_job_trans');
    }
};
