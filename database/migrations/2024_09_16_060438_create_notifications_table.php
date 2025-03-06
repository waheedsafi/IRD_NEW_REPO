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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userable_id');
            $table->string('userable_type');
            $table->unsignedBigInteger('notifier_type_id');
            $table->foreign('notifier_type_id')->references('id')->on('notifier_types')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->boolean("read_status")->default(false);
            $table->string("message");
            $table->index(['notifier_type_id', "userable_id", 'userable_type']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
