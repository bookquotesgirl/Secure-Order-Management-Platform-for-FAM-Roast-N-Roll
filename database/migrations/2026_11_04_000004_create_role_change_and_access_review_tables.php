<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Separation of duties: approver_id must differ from requested_by.
        // Enforced in RoleChangePolicy and by the CHECK constraint below (MySQL 8.0.16+).
        Schema::create('role_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_user_id')->constrained('users');
            $table->foreignId('from_role_id')->nullable()->constrained('roles');
            $table->foreignId('to_role_id')->constrained('roles');
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->string('justification', 500);
            $table->string('decision_note', 500)->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::getConnection()->statement(
            'ALTER TABLE role_change_requests ADD CONSTRAINT chk_sod CHECK (approver_id IS NULL OR approver_id <> requested_by)'
        );

        // Periodic (e.g. quarterly) review of who holds which role.
        Schema::create('access_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('period', 20);                    // e.g. 2026-Q4
            $table->foreignId('reviewer_id')->constrained('users');
            $table->timestamp('completed_at')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
            $table->unique('period');
        });

        Schema::create('access_review_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->enum('decision', ['keep', 'revoke', 'change'])->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_review_items');
        Schema::dropIfExists('access_reviews');
        Schema::dropIfExists('role_change_requests');
    }
};
