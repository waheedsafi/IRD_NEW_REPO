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
            $table->integer('type')->comment('Can be either ngo or project');
            $table->unsignedBigInteger('represented_id')->comment('if type is ngo this id refer to agreement id else project');
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
