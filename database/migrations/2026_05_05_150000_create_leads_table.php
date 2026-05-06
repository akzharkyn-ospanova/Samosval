<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('contacts')->nullable();
            $table->text('comment')->nullable();
            $table->string('source')->nullable();
            $table->tinyInteger('status')->default(0); // 0 - новая, 1 - в работе, 2 - закрыта
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
