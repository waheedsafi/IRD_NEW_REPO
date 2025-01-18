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
        Schema::create('project_trans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')
                ->onUpdate('cascade')
                ->onDelete('no action');
          $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('preamble');
            $table->string('health_experience');
            $table->string('goals');
            $table->string('objectives');
            $table->string('expected_outcome');
            $table->string('expected_impact');
            $table->string('subject');
            $table->string('main_activities');
            $table->string('introduction');
            $table->string('operational_plan');
            $table->string('mission');
            $table->string('vission');
                
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_trans');
    }
};
