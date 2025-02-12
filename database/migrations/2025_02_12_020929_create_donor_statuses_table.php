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
        Schema::create('donor_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donor_id');
            $table->boolean('is_active')->default(false);
            $table->foreign('donor_id')->references('id')->on('donors')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('status_type_id');
            $table->foreign('status_type_id')->references('id')->on('status_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('comment', 128);
            $table->foreignId('user_id')
                ->constrained()
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
        Schema::dropIfExists('donor_statuses');
    }
};
