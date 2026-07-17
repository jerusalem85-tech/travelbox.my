<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets (1)
            ['code' => '1', 'name' => 'Assets', 'type' => 'asset', 'parent_id' => null],
            ['code' => '1-001', 'name' => 'Cash & Bank', 'type' => 'asset', 'parent_id' => null],
            ['code' => '1-002', 'name' => 'Accounts Receivable', 'type' => 'asset', 'parent_id' => null],
            ['code' => '1-003', 'name' => 'Prepaid Expenses', 'type' => 'asset', 'parent_id' => null],

            // Liabilities (2)
            ['code' => '2', 'name' => 'Liabilities', 'type' => 'liability', 'parent_id' => null],
            ['code' => '2-001', 'name' => 'Accounts Payable', 'type' => 'liability', 'parent_id' => null],
            ['code' => '2-002', 'name' => 'Customer Deposits', 'type' => 'liability', 'parent_id' => null],
            ['code' => '2-003', 'name' => 'Tax Payable', 'type' => 'liability', 'parent_id' => null],

            // Equity (3)
            ['code' => '3', 'name' => 'Equity', 'type' => 'equity', 'parent_id' => null],
            ['code' => '3-001', 'name' => 'Owner\'s Capital', 'type' => 'equity', 'parent_id' => null],
            ['code' => '3-002', 'name' => 'Retained Earnings', 'type' => 'equity', 'parent_id' => null],

            // Income (4)
            ['code' => '4', 'name' => 'Income', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-001', 'name' => 'Flight Revenue', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-002', 'name' => 'Hotel Revenue', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-003', 'name' => 'Transfer Revenue', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-004', 'name' => 'Visa Revenue', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-005', 'name' => 'Insurance Revenue', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-006', 'name' => 'Activity Revenue', 'type' => 'income', 'parent_id' => null],
            ['code' => '4-007', 'name' => 'Service Fees', 'type' => 'income', 'parent_id' => null],

            // Expenses (5)
            ['code' => '5', 'name' => 'Expenses', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-001', 'name' => 'Flight Cost', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-002', 'name' => 'Hotel Cost', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-003', 'name' => 'Transfer Cost', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-004', 'name' => 'Visa Cost', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-005', 'name' => 'Insurance Cost', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-006', 'name' => 'Activity Cost', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-007', 'name' => 'Commission Expense', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-008', 'name' => 'Bank Charges', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-009', 'name' => 'Office Expenses', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-010', 'name' => 'Salaries & Wages', 'type' => 'expense', 'parent_id' => null],
            ['code' => '5-011', 'name' => 'Marketing & Advertising', 'type' => 'expense', 'parent_id' => null],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create($account);
        }
    }
}
