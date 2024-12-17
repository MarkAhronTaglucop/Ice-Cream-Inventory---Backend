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
    Schema::create('icecreams', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->date('manufactured_date');
        $table->string('image_url')->nullable(); // Add this column for storing the image URL or path
        $table->timestamps();
    });

    Schema::create('prices', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('icecream_id');
        $table->decimal('price', 8, 2);
        $table->timestamps();
    
        $table->foreign('icecream_id')->references('id')->on('icecreams')->onDelete('cascade');
    });

    Schema::create('stocks', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('icecream_id');
        $table->integer('stock');
        $table->enum('status', ['Available', 'Out of Stock'])->default('Available');
        $table->timestamps();
    
        $table->foreign('icecream_id')->references('id')->on('icecreams')->onDelete('cascade');
    });
    
    
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icecreams');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('prices');


    }
};
