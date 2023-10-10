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
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('user_id');

            $table->date('assignment_date');
            $table->string('status');
            $table->text('comments')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignments');
    }
};
