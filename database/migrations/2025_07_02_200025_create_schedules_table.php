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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('representators_count');
            $table->integer('presentation_lenght');
            $table->integer('gap_between');
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->time('dinner_start')->nullable();
            $table->time('dinner_end')->nullable();
            $table->integer('presentation_before_lunch');
            $table->integer('presentation_after_lunch');
            $table->boolean('is_hour_24');
            $table->unsignedBigInteger('schedule_status_id');
            $table->foreign('schedule_status_id')->references('id')->on('schedule_statuses')
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
        Schema::dropIfExists('schedules');
    }
};
