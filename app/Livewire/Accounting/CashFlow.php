<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryItem;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class CashFlow extends Component
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
        $cashAccounts = ChartOfAccount::whereIn('code', ['1100', '1101', '1102'])
            ->orWhere('name', 'like', '%cash%')
            ->orWhere('name', 'like', '%bank%')
            ->where('is_active', true)->get();
        $cashIds = $cashAccounts->pluck('id');

        $operatingIncome = (float) JournalEntryItem::whereIn('account_id', $cashIds)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$this->from, $this->to]))
            ->whereHas('account', fn($q) => $q->where('type', 'income'))
            ->sum('debit');

        $operatingExpenses = (float) JournalEntryItem::whereIn('account_id', $cashIds)
            ->whereHas('journalEntry', fn($q) => $q->whereBetween('date', [$this->from, $this->to]))
            ->whereHas('account', fn($q) => $q->where('type', 'expense'))
            ->sum('credit');

        $openingBalance = (float) JournalEntryItem::whereIn('account_id', $cashIds)
            ->whereHas('journalEntry', fn($q) => $q->where('date', '<', $this->from))
            ->sum('debit') - (float) JournalEntryItem::whereIn('account_id', $cashIds)
            ->whereHas('journalEntry', fn($q) => $q->where('date', '<', $this->from))
            ->sum('credit');

        $periodChange = $operatingIncome - $operatingExpenses;
        $closingBalance = $openingBalance + $periodChange;

        return view('livewire.accounting.cash-flow', compact(
            'openingBalance', 'operatingIncome', 'operatingExpenses', 'periodChange', 'closingBalance'
        ));
    }
}
