<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'trips.create', 'trips.edit', 'trips.delete', 'trips.view',
            'customers.create', 'customers.edit', 'customers.delete', 'customers.view',
            'suppliers.create', 'suppliers.edit', 'suppliers.delete', 'suppliers.view',
            'payments.create', 'payments.edit', 'payments.delete', 'payments.view',
            'payments.approve', 'payments.refund',
            'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.view',
            'accounting.view', 'accounting.manage',
            'reports.view.sales', 'reports.view.profit', 'reports.view.customers',
            'reports.view.suppliers', 'reports.view.cashflow', 'reports.view.outstanding',
            'documents.create', 'documents.edit', 'documents.delete', 'documents.view',
            'documents.templates',
            'settings.manage', 'users.manage', 'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo(Permission::all()->except([
            'settings.manage', 'users.manage', 'roles.manage',
        ]));

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'trips.create', 'trips.edit', 'trips.view',
            'customers.create', 'customers.edit', 'customers.view',
            'suppliers.create', 'suppliers.edit', 'suppliers.view',
            'payments.create', 'payments.edit', 'payments.view',
            'invoices.create', 'invoices.edit', 'invoices.view',
            'accounting.view',
            'reports.view.sales', 'reports.view.profit', 'reports.view.customers',
            'reports.view.suppliers', 'reports.view.cashflow', 'reports.view.outstanding',
            'documents.create', 'documents.edit', 'documents.view',
        ]);

        $sales = Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
        $sales->givePermissionTo([
            'trips.create', 'trips.edit', 'trips.view',
            'customers.create', 'customers.edit', 'customers.view',
            'invoices.view',
            'documents.create', 'documents.view',
        ]);

        $operations = Role::firstOrCreate(['name' => 'operations', 'guard_name' => 'web']);
        $operations->givePermissionTo([
            'trips.view', 'trips.edit',
            'customers.view', 'suppliers.view',
            'documents.create', 'documents.edit', 'documents.view',
        ]);

        $accountant = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountant->givePermissionTo([
            'payments.create', 'payments.edit', 'payments.view', 'payments.approve',
            'invoices.create', 'invoices.edit', 'invoices.view',
            'accounting.view', 'accounting.manage',
            'reports.view.sales', 'reports.view.profit', 'reports.view.outstanding',
            'reports.view.cashflow',
            'documents.view',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->givePermissionTo([
            'trips.view', 'customers.view', 'suppliers.view',
            'payments.view', 'invoices.view', 'accounting.view',
            'documents.view',
        ]);
    }
}
