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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description'); // (Deskripsi tugas)
            $table->unsignedBigInteger('created_by'); //(Foreign Key referencing Users table, ID pencipta tugas)
            $table->unsignedBigInteger('assigned_to')->nullable(); //(Foreign Key referencing Users table, ID penerima tugas, bisa NULL jika belum ditugaskan)
            $table->string('status'); //status (Status tugas, misalnya ongoing, completed, pending, dll)
            $table->date('due_date'); // (Batas waktu tugas)
            $table->timestamp('completed_at')->nullable(); // (Datetime field, NULL jika belum selesai)
            $table->string('priority'); //(Prioritas tugas, misalnya rendah, sedang, tinggi)
            $table->integer('estimated_hours'); // (Estimasi waktu yang dibutuhkan untuk menyelesaikan tugas)
            $table->integer('actual_hours')->nullable(); //(Waktu sebenarnya yang diperlukan untuk menyelesaikan tugas)
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
