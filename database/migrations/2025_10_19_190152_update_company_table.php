<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('companies', 'standard_rate')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('standard_rate');
            });
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('standard_rate', 8, 2)->nullable();
        });
    }
};

