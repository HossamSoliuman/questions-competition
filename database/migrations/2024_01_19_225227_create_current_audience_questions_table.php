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
        Schema::create('current_audience_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('show_question')->default(0);
            $table->boolean('show_answer')->default(0);
            $table->text('random_number')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_audience_questions');
    }
};
