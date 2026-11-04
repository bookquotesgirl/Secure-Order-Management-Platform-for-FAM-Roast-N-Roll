<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * LOCAL/DEMO ONLY. Creates fake accounts (no real people) with random passwords,
 * printed once to the console. Refuses to run in production. Two admins exist so the
 * separation-of-duties demo (one requests, the other approves) is possible.
 */
class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command->error('DemoUserSeeder must not run in production.');
            return;
        }

        $yerer = DB::table('branches')->where('slug', 'yerer')->value('id');
        $bole  = DB::table('branches')->where('slug', 'bole')->value('id');

        $accounts = [
            ['admin1@demo.test',   'Demo Admin One',     'admin',            null],
            ['admin2@demo.test',   'Demo Admin Two',     'admin',            null],
            ['auditor@demo.test',  'Demo Auditor',       'security_auditor', null],
            ['manager.yerer@demo.test', 'Demo Manager Yerer', 'branch_manager', $yerer],
            ['staff.yerer@demo.test',   'Demo Staff Yerer',   'order_staff',    $yerer],
            ['staff.bole@demo.test',    'Demo Staff Bole',    'order_staff',    $bole],
            ['customer@demo.test', 'Demo Customer',      'customer',         null],
        ];

        $this->command->warn('Demo credentials (shown once, store them yourself):');
        foreach ($accounts as [$email, $name, $role, $branchId]) {

    // Don't generate a new password for an account that already exists.
    if (DB::table('users')->where('email', $email)->exists()) {
        $this->command->warn("Skipping existing account: {$email}");
        continue;
    }

    $password = Str::password(16);

    DB::table('users')->insert([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'role_id' => DB::table('roles')->where('name', $role)->value('id'),
        'branch_id' => $branchId,
        'email_verified_at' => now(),
        'password_changed_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->command->line("{$email}  {$password}");
}
    }
}
