<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryItem;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class ProfitLoss extends Component
{
    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public function mount(): void
    {
        if (!$this->from) $this->from = now()->startOfYear()->format('Y-m-d');
        if (!$this->to) $this->to = now()->format('Y-m-d');
    }

    public function render()
    {
        $incomeAccounts = ChartOfAccount::where('type', 'income')->where('is_active', true)->orderBy('code')->get();
        $expenseAccounts = ChartOfAccount::where('type', 'expense')->where('is_active', true)->orderBy('code')->get();

        $totalIncome = 0;
        $totalExpense = 0;
        $incomeItems = [];
        $expenseItems = [];

        foreach ($incomeAccounts as $acc) {
            $amount = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$this->from, $this->to]))
                ->sum('credit') - (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$this->from, $this->to]))
                ->sum('debit');
            if ($amount != 0) {
                $incomeItems[] = (object)['name' => $acc->name, 'amount' => $amount];
                $totalIncome += $amount;
            }
        }

        foreach ($expenseAccounts as $acc) {
            $amount = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$this->from, $this->to]))
                ->sum('debit') - (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$this->from, $this->to]))
                ->sum('credit');
            if ($amount != 0) {
                $expenseItems[] = (object)['name' => $acc->name, 'amount' => $amount];
                $totalExpense += $amount;
            }
        }

        $netProfit = $totalIncome - $totalExpense;

        return view('livewire.accounting.profit-loss', compact('incomeItems', 'expenseItems', 'totalIncome', 'totalExpense', 'netProfit'));
    }
}
