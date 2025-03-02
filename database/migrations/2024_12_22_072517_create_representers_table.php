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
        Schema::create('representers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('ngo_id');
            $table->foreign('ngo_id')->references('id')->on('ngos')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('document_id')->nullable();
            $table->foreign('document_id')->references('id')->on('documents')
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
        Schema::dropIfExists('ngo_representers');
    }
};
