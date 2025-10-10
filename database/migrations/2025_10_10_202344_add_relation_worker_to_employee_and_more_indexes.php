<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * USERS TABLE
         */
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'worker_id')) {
                $table->foreignId('worker_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('workers')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'role_context')) {
                $table->enum('role_context', ['main_company', 'subcontractor', 'employee'])
                    ->default('employee')
                    ->after('worker_id');
            }

            $table->index('worker_id', 'idx_users_worker');
        });

        /**
         * WORKERS TABLE
         */
        if (!$this->foreignKeyExists('workers', 'workers_employee_id_foreign')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->foreign('employee_id')
                    ->references('id')
                    ->on('employees')
                    ->nullOnDelete();
            });
        }

        Schema::table('workers', function (Blueprint $table) {
            $table->index(['subcontractor_id', 'employee_id'], 'idx_workers_sub_employee');
        });

        /**
         * EMPLOYEES TABLE
         */
        Schema::table('employees', function (Blueprint $table) {
            $table->index('user_id', 'idx_employees_user');
        });

        /**
         * SUBCONTRACTORS TABLE
         */
        Schema::table('subcontractors', function (Blueprint $table) {
            $table->index('parent_subcontractor_id', 'idx_subcontractors_parent');
        });

        /**
         * TIMESHEETS TABLE
         */
        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->index(['worker_id', 'project_id'], 'idx_timesheets_worker_project');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'worker_id')) {
                $table->dropForeign(['worker_id']);
                $table->dropIndex('idx_users_worker');
                $table->dropColumn('worker_id');
            }

            if (Schema::hasColumn('users', 'role_context')) {
                $table->dropColumn('role_context');
            }
        });

        if ($this->foreignKeyExists('workers', 'workers_employee_id_foreign')) {
            Schema::table('workers', function (Blueprint $table) {
                $table->dropForeign('workers_employee_id_foreign');
            });
        }

        Schema::table('workers', function (Blueprint $table) {
            $table->dropIndex('idx_workers_sub_employee');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_user');
        });

        Schema::table('subcontractors', function (Blueprint $table) {
            $table->dropIndex('idx_subcontractors_parent');
        });

        if (Schema::hasTable('timesheets')) {
            Schema::table('timesheets', function (Blueprint $table) {
                $table->dropIndex('idx_timesheets_worker_project');
            });
        }
    }

    /**
     * Check if a foreign key exists in PostgreSQL
     */
    private function foreignKeyExists(string $table, string $constraint): bool
    {
        $result = DB::selectOne("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY'
        ", [$table, $constraint]);

        return !empty($result);
    }
};
