<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds branches, roles, permissions and the least-privilege matrix.
 * Source of truth for docs/permission-matrix.md.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('branches')->insertOrIgnore([
            ['slug' => 'yerer', 'name' => 'Yerer Gulit Market', 'address' => 'Between Fiker Restaurant and Beka Bakery, Yerer Gulit Market, Addis Ababa', 'phone' => '+251910492141', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'bole', 'name' => 'Bole Bulbula', 'address' => '93 Mazoria, in front of Jenboro Gym, Bole Bulbula, Addis Ababa', 'phone' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $permissions = [
            'order.create'            => 'Place an order',
            'order.view.own'          => 'View own order history and status',
            'order.view.branch'       => 'View orders of own branch',
            'order.update_status'     => 'Change status of own-branch orders',
            'menu.manage'             => 'Create, edit and hide menu items',
            'user.view.branch'        => 'View staff accounts of own branch',
            'user.manage'             => 'Create, deactivate and unlock accounts',
            'role.request_change'     => 'Request a role change for a user',
            'role.approve_change'     => 'Approve or reject role-change requests (never own)',
            'audit.view'              => 'Read audit logs',
            'audit.export'            => 'Export audit logs and run integrity check',
            'security.alerts.view'    => 'View security alerts dashboard',
            'access.review'           => 'Run the periodic access review',
            'backup.manage'           => 'Run and restore backups, view backup logs',
        ];
        foreach ($permissions as $name => $description) {
            DB::table('permissions')->insertOrIgnore(compact('name', 'description') + ['created_at' => $now, 'updated_at' => $now]);
        }

        $roles = [
            'customer'         => ['Registered customer', false, ['order.create', 'order.view.own']],
            'order_staff'      => ['Branch kitchen/counter staff', false, ['order.view.branch', 'order.update_status']],
            'branch_manager'   => ['Manages one branch', true, ['order.view.branch', 'order.update_status', 'menu.manage', 'user.view.branch', 'role.request_change']],
            'admin'            => ['System administrator', true, ['user.manage', 'menu.manage', 'role.request_change', 'role.approve_change', 'access.review', 'backup.manage', 'security.alerts.view']],
            'security_auditor' => ['Read-only security oversight', true, ['audit.view', 'audit.export', 'security.alerts.view']],
        ];

        foreach ($roles as $name => [$description, $privileged, $perms]) {
            DB::table('roles')->insertOrIgnore([
                'name' => $name, 'description' => $description, 'is_privileged' => $privileged,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            $roleId = DB::table('roles')->where('name', $name)->value('id');
            foreach ($perms as $perm) {
                $permId = DB::table('permissions')->where('name', $perm)->value('id');
                DB::table('permission_role')->insertOrIgnore(['role_id' => $roleId, 'permission_id' => $permId]);
            }
        }
    }
}
