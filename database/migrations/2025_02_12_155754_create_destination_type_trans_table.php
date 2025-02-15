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
        Schema::create('destination_type_trans', function (Blueprint $table) {
            $table->id();
            $table->string('value', 128);
            $table->unsignedBigInteger('destination_type_id');
            $table->foreign('destination_type_id')->references('id')->on('destination_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
            $table->index(["language_name", "destination_type_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_type_trans');
    }
};
