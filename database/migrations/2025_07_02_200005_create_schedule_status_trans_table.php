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
        Schema::create('schedule_status_trans', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->unsignedBigInteger('schedule_status_id');
            $table->foreign('schedule_status_id')->references('id')->on('schedule_statuses')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index(["language_name", "schedule_status_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_status_trans');
    }
};
