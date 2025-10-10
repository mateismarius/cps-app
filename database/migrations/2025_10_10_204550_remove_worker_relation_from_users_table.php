<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // verificăm dacă FK există (pentru PostgreSQL)
            try {
                $table->dropForeign(['worker_id']);
            } catch (\Throwable $e) {
                // dacă FK-ul nu există, ignorăm eroarea
            }

            if (Schema::hasColumn('users', 'worker_id')) {
                $table->dropIndex('idx_users_worker'); // dacă există
                $table->dropColumn('worker_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'worker_id')) {
                $table->foreignId('worker_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('workers')
                    ->nullOnDelete();

                $table->index('worker_id', 'idx_users_worker');
            }
        });
    }
};
