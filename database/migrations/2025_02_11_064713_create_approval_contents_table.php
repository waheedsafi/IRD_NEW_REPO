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
        Schema::create('approval_contents', function (Blueprint $table) {
            $table->id();
            $table->string("file_path");
            $table->unsignedBigInteger("approveable_it")->comment("Primary of table on which");
            $table->unsignedBigInteger("approval_id");
            $table->foreign('approval_id')->references('id')->on('approvals')
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
        Schema::dropIfExists('approval_contents');
    }
};
