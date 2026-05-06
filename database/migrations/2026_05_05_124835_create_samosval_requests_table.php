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
        Schema::create('samosval_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('samosval_id')
                ->constrained('samosvals')
                ->onDelete('cascade');

            $table->foreignId('problem_id')
                ->constrained('samosval_problems')
                ->onDelete('cascade');

            $table->unsignedTinyInteger('status')->default(0);
            $table->foreignId('solution_id')
                ->nullable()
                ->constrained('samosval_solutions')
                ->onDelete('set null');

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samosval_requests');
    }
};
