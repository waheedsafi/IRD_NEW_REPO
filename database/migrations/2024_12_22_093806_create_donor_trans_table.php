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
        Schema::create('donor_trans', function (Blueprint $table) {
            $table->id();
                $table->unsignedBigInteger('donor_id');
            $table->foreign('donor_id')->references('id')->on('donors')
                ->onUpdate('cascade')
                ->onDelete('no action');

                $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')->onUpdate('cascade')
                ->onDelete('cascade');
                $table->string('name',64);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_trans');
    }
};
