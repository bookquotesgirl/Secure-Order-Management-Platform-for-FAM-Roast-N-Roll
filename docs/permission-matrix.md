# Role-Permission Matrix

Source of truth: `database/seeders/RolePermissionSeeder.php`. Least privilege: each role gets only what its job needs.

| Permission | customer | order_staff | branch_manager | admin | security_auditor |
| --- | :-: | :-: | :-: | :-: | :-: |
| order.create | ✔ | | | | |
| order.view.own | ✔ | | | | |
| order.view.branch | | ✔ | ✔ | | |
| order.update_status | | ✔ | ✔ | | |
| menu.manage | | | ✔ | ✔ | |
| user.view.branch | | | ✔ | | |
| user.manage | | | | ✔ | |
| role.request_change | | | ✔ | ✔ | |
| role.approve_change | | | | ✔ | |
| access.review | | | | ✔ | |
| backup.manage | | | | ✔ | |
| security.alerts.view | | | | ✔ | ✔ |
| audit.view | | | | | ✔ |
| audit.export | | | | | ✔ |

## Deliberate design choices

- **Branch scoping.** `order.view.branch`, `order.update_status` and `user.view.branch` apply only to the user's own `branch_id`. A Bole staff member cannot read Yerer orders (negative test required).
- **Auditor is read-only.** The auditor can read logs and alerts but cannot change users, orders, menu or roles.
- **Admin cannot read audit logs or orders.** Administrators manage accounts and backups; oversight belongs to a different person (separation of duties).
- **Role-change workflow.** A manager or admin files a request (`role_change_requests`). A different admin approves or rejects. The approver can never be the requester, enforced in the policy and by a database CHECK constraint. Changes to privileged roles (`roles.is_privileged = true`) always need approval.
- **Demo accounts.** Two admins are seeded so the requester/approver demo works.

## Negative tests to write (PHPUnit)

1. A customer requesting `/staff/orders` is denied and logged.
2. A Bole staff member requesting a Yerer order is denied.
3. An admin approving their own role-change request is rejected.
4. An auditor attempting to update a user or order is denied.
5. Order staff attempting `menu.manage` is denied.
