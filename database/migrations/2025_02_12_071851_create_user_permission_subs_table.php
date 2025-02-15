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
        Schema::create('user_permission_subs', function (Blueprint $table) {
            $table->id();
            $table->boolean('edit');
            $table->boolean('delete');
            $table->boolean('add');
            $table->boolean('view');
            $table->unsignedBigInteger('user_permission_id');
            $table->foreign('user_permission_id')->references('id')->on('user_permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->unsignedBigInteger('sub_permission_id');
            $table->foreign('sub_permission_id')->references('id')->on('sub_permissions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->index(["user_permission_id", "sub_permission_id"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permission_subs');
    }
};
