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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_company_id')
                ->constrained('companies')->cascadeOnDelete();
            $table->foreignId('to_company_id')
                ->constrained('companies')->cascadeOnDelete();

            $table->decimal('rate_value', 8, 2);
            $table->string('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
