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
        // Performance indexes for performance_agreements table
        Schema::table('performance_agreements', function (Blueprint $table) {
            $table->index(['employee_id', 'year'], 'pa_employee_year_idx');
            $table->index(['status', 'year'], 'pa_status_year_idx');
            $table->index(['created_by', 'status'], 'pa_creator_status_idx');
            $table->index('approved_by', 'pa_approved_by_idx');
        });

        // Performance indexes for monthly_assessments table
        Schema::table('monthly_assessments', function (Blueprint $table) {
            $table->index(['agreement_id', 'month', 'year'], 'ma_agreement_month_year_idx');
            $table->index(['assessor_id', 'status'], 'ma_assessor_status_idx');
            $table->index(['supervisor_id', 'status'], 'ma_supervisor_status_idx');
            $table->index(['status', 'year'], 'ma_status_year_idx');
        });

        // Performance indexes for performance_targets table
        Schema::table('performance_targets', function (Blueprint $table) {
            $table->index('agreement_id', 'pt_agreement_idx');
        });

        // Performance indexes for assessment_details table
        Schema::table('assessment_details', function (Blueprint $table) {
            $table->index(['assessment_id', 'target_id'], 'ad_assessment_target_idx');
        });

        // Performance indexes for documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->index(['assessment_id', 'uploaded_by'], 'doc_assessment_uploader_idx');
            $table->index('uploaded_by', 'doc_uploader_idx');
        });

        // Performance indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'users_role_idx');
            $table->index('is_active', 'users_active_idx');
        });

        // Performance indexes for employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['user_id', 'employee_status'], 'emp_user_status_idx');
            $table->index('work_unit_id', 'emp_work_unit_idx');
            $table->index('nip', 'emp_nip_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes for performance_agreements table
        Schema::table('performance_agreements', function (Blueprint $table) {
            $table->dropIndex('pa_employee_year_idx');
            $table->dropIndex('pa_status_year_idx');
            $table->dropIndex('pa_creator_status_idx');
            $table->dropIndex('pa_approved_by_idx');
        });

        // Drop indexes for monthly_assessments table
        Schema::table('monthly_assessments', function (Blueprint $table) {
            $table->dropIndex('ma_agreement_month_year_idx');
            $table->dropIndex('ma_assessor_status_idx');
            $table->dropIndex('ma_supervisor_status_idx');
            $table->dropIndex('ma_status_year_idx');
        });

        // Drop indexes for performance_targets table
        Schema::table('performance_targets', function (Blueprint $table) {
            $table->dropIndex('pt_agreement_idx');
        });

        // Drop indexes for assessment_details table
        Schema::table('assessment_details', function (Blueprint $table) {
            $table->dropIndex('ad_assessment_target_idx');
        });

        // Drop indexes for documents table
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('doc_assessment_uploader_idx');
            $table->dropIndex('doc_uploader_idx');
        });

        // Drop indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_idx');
            $table->dropIndex('users_active_idx');
        });

        // Drop indexes for employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('emp_user_status_idx');
            $table->dropIndex('emp_work_unit_idx');
            $table->dropIndex('emp_nip_idx');
        });
    }
};
