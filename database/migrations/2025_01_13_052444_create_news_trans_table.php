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
        Schema::create('news_trans', function (Blueprint $table) {
            $table->id(); 
        
            $table->unsignedBigInteger('news_id');
            $table->foreign('news_id')->references('id')->on('news')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('language_name');
            $table->foreign('language_name')->references('name')->on('languages')->onUpdate('cascade')
             ->onDelete('cascade');
            $table->string('title');
            $table->longText('contents');
            $table->index('language_name','news_id');
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_trans');
    }
};
