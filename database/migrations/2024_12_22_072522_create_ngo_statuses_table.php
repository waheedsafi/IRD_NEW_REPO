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
        Schema::create('ngo_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ngo_id');
            $table->boolean('is_active')->default(false);
            $table->foreign('ngo_id')->references('id')->on('ngos')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('status_id');
            $table->foreign('status_id')->references('id')->on('statuses')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->text('comment', 128);
            $table->string('userable_type');
            $table->unsignedBigInteger('userable_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngo_statuses');
    }
};
