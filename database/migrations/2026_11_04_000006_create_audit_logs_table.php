<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tamper-evident audit log. Each row stores hash = SHA-256(prev_hash + canonical row data),
 * forming a chain; `php artisan audit:verify` (to be built) recomputes it. On MySQL/MariaDB,
 * triggers also reject UPDATE and DELETE so the table is append-only for the app's DB user.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('occurred_at', 6);
            $table->foreignId('user_id')->nullable();         // no FK: logs must outlive users
            $table->string('ip_address', 45)->nullable();
            $table->string('event', 60)->index();             // e.g. auth.login.failed, role.change.approved
            $table->string('outcome', 12);                    // success | failure | denied
            $table->string('subject_type', 60)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('context')->nullable();              // never passwords, tokens or secrets
            $table->char('prev_hash', 64);
            $table->char('hash', 64)->unique();
            $table->index('occurred_at');
            $table->index('user_id');
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::unprepared("CREATE TRIGGER audit_logs_no_update BEFORE UPDATE ON audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only'");
            DB::unprepared("CREATE TRIGGER audit_logs_no_delete BEFORE DELETE ON audit_logs FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'audit_logs is append-only'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_update');
            DB::unprepared('DROP TRIGGER IF EXISTS audit_logs_no_delete');
        }
        Schema::dropIfExists('audit_logs');
    }
};
