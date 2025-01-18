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
        Schema::create('donor_permissions', function (Blueprint $table) {
         $table->id();
            $table->boolean('view');
            $table->boolean('edit');
            $table->boolean('delete');
            $table->boolean('add');
            $table->foreignId('donor_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('permission');
            $table->foreign('permission')->references('name')->on('permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index(["permission", "donor_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_permissions');
    }
};
