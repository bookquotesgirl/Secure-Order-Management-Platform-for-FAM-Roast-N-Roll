<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends Laravel's default users table (created by 0001_01_01_000000_create_users_table).
 * Data classification: password = restricted (hashed), mfa_secret = restricted (encrypted),
 * phone/email = confidential.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles');
            $table->foreignId('branch_id')->nullable()->after('role_id')->constrained('branches'); // null = not branch-scoped
            $table->string('phone', 30)->nullable()->after('email');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->text('mfa_secret')->nullable();          // store via Laravel 'encrypted' cast
            $table->timestamp('mfa_confirmed_at')->nullable();
        });

        Schema::create('mfa_recovery_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('code_hash', 64);                    // SHA-256 of the code; shown to the user once
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mfa_recovery_codes');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn([
                'phone', 'is_active', 'failed_login_attempts', 'locked_until',
                'last_login_at', 'password_changed_at', 'mfa_secret', 'mfa_confirmed_at',
            ]);
        });
    }
};
