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
        Schema::create('approval_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documentable_id');
            $table->string('documentable_type');
            $table->unsignedBigInteger('approval_id');
            $table->foreign('approval_id')->references('id')->on('approvals')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps();
            $table->index(['approval_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_documents');
    }
};
