<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['employee', 'subcontractor_ltd', 'self_employed'])
                ->nullable()
                ->after('email');
            // Dacă vrei să legi userul de un subcontractor (ex: manager LTD sau self-employed):
            $table->foreignId('subcontractor_id')->nullable()->after('user_type')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subcontractor_id');
            $table->dropColumn('user_type');
        });
    }
};
