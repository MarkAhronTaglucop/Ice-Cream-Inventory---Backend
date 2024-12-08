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
    Schema::create('icecream', function (Blueprint $table) {
        $table->id('ICECREAM_ID');
        $table->string('name');
        $table->decimal('price', 8, 2);
        $table->integer('stock');
        $table->date('manufactured_date');
        $table->text('description')->nullable();
        $table->string('image')->nullable();
        $table->enum('status', ['Available', 'Out of Stock'])->default('Available');
        $table->timestamps();
    
        // Foreign key column
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
    });
    
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flavors');
    }
};
