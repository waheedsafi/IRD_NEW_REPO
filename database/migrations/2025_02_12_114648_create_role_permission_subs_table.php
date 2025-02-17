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
        Schema::create('role_permission_subs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_permission_id');
            $table->foreign('role_permission_id')->references('id')->on('role_permissions')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->unsignedBigInteger('sub_permission_id');
            $table->foreign('sub_permission_id')->references('id')->on('sub_permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index(["role_permission_id", "sub_permission_id"], 'role_permission_subs_permission_index');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permission_subs');
    }
};
