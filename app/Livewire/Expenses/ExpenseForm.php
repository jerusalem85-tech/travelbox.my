<?php

namespace App\Livewire\Expenses;

use App\Models\GeneralExpense;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ExpenseForm extends Component
{
    public ?GeneralExpense $expense = null;
    public bool $editing = false;

    public string $category = 'office';
    public string $description = '';
    public string $amount = '0';
    public string $currency = 'USD';
    public string $expense_date = '';
    public string $payment_method = '';
    public string $reference = '';
    public string $vendor = '';
    public string $notes = '';
    public string $status = 'pending';

    public array $categories = [
        'office' => 'Office Expenses',
        'utilities' => 'Utilities',
        'marketing' => 'Marketing',
        'salaries' => 'Salaries',
        'travel' => 'Travel',
        'other' => 'Other',
    ];

    public function mount(?GeneralExpense $expense = null): void
    {
        $this->expense = $expense;
        if ($expense) {
            $this->editing = true;
            $this->category = $expense->category;
            $this->description = $expense->description;
            $this->amount = (string) $expense->amount;
            $this->currency = $expense->currency;
            $this->expense_date = $expense->expense_date->format('Y-m-d');
            $this->payment_method = $expense->payment_method ?? '';
            $this->reference = $expense->reference ?? '';
            $this->vendor = $expense->vendor ?? '';
            $this->notes = $expense->notes ?? '';
            $this->status = $expense->status;
        } else {
            $this->expense_date = now()->format('Y-m-d');
        }
    }

    public function rules(): array
    {
        return [
            'category' => 'required|in:office,utilities,marketing,salaries,travel,other',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'expense_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,paid,cancelled',
        ];
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'category' => $this->category,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'expense_date' => $this->expense_date,
            'payment_method' => $this->payment_method ?: null,
            'reference' => $this->reference ?: null,
            'vendor' => $this->vendor ?: null,
            'notes' => $this->notes ?: null,
            'status' => $this->status,
        ];

        if ($this->editing && $this->expense) {
            $this->expense->update($data);
            $this->dispatch('notify', type: 'success', title: 'Updated', message: 'Expense updated.');
        } else {
            $data['created_by'] = auth()->id() ?? 1;
            GeneralExpense::create($data);

            try {
                app(\App\Services\AccountingService::class)->postExpense(
                    'general_expense', $data['amount'], $data['expense_date'], $data['category'] . ': ' . $data['description']
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Expense accounting failed: ' . $e->getMessage());
            }

            $this->dispatch('notify', type: 'success', title: 'Created', message: 'Expense created.');
        }

        $this->dispatch('expense-saved');
    }

    public function render()
    {
        return view('livewire.expenses.expense-form');
    }
}
