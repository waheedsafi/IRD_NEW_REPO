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
        Schema::create('check_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_list_type_id');
            $table->foreign('check_list_type_id')->references('id')->on('check_list_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('name',64);
            $table->string('file_extensions'); 
            $table->string('description',128);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_lists');
    }
};
