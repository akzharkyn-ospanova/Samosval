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
        Schema::create('samosvals', function (Blueprint $table) {
            $table->id();

            $table->unsignedTinyInteger('status')->default(0);
            $table->string('system_id')->nullable();
            $table->string('address')->nullable();
            $table->string('serial_number')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Samosvals');
    }
};
