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
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');
            $table->text('log_text');
            $table->timestamp('log_date');
            $table->string('activity_type');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_logs');
    }
};
