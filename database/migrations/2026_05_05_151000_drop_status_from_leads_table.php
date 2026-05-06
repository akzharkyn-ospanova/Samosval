<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('source');
        });
    }
};
