<?php

namespace App\Livewire\Expenses;

use App\Models\GeneralExpense;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class ExpenseList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $category = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sortField = 'expense_date';

    #[Url]
    public string $sortDirection = 'desc';

    public bool $showForm = false;
    public ?GeneralExpense $editingExpense = null;

    public array $categories = [
        'office' => 'Office Expenses',
        'utilities' => 'Utilities',
        'marketing' => 'Marketing',
        'salaries' => 'Salaries',
        'travel' => 'Travel',
        'other' => 'Other',
    ];

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create(): void
    {
        $this->editingExpense = null;
        $this->showForm = true;
    }

    public function edit(GeneralExpense $expense): void
    {
        $this->editingExpense = $expense;
        $this->showForm = true;
    }

    #[On('expense-saved')]
    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingExpense = null;
    }

    public function delete(GeneralExpense $expense): void
    {
        $expense->delete();
        $this->dispatch('notify', type: 'success', title: 'Deleted', message: 'Expense deleted.');
    }

    public function render()
    {
        $query = GeneralExpense::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', "%{$this->search}%")
                  ->orWhere('vendor', 'like', "%{$this->search}%")
                  ->orWhere('reference', 'like', "%{$this->search}%");
            });
        }

        if ($this->category) {
            $query->where('category', $this->category);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        $totalByCategory = GeneralExpense::selectRaw('category, SUM(amount) as total')
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->groupBy('category')->pluck('total', 'category');

        return view('livewire.expenses.expense-list', [
            'expenses' => $query->paginate(15),
            'totalByCategory' => $totalByCategory,
        ]);
    }
}
