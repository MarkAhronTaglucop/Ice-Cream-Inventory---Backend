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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id('LOG_ID');
            $table->foreignId('user_id')
                  ->constrained('users', 'id') // Links to 'users' table
                  ->onDelete('cascade');
            $table->string('action'); // e.g., View, Add, Edit
            $table->string('table_name'); // Name of the table affected
            $table->integer('record_id')->nullable(); // ID of the record affected
            $table->timestamp('timestamp')->useCurrent();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
