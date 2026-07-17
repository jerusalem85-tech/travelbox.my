<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryItem;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class BalanceSheet extends Component
{
    #[Url]
    public string $as_of = '';

    public function mount(): void
    {
        if (!$this->as_of) $this->as_of = now()->format('Y-m-d');
    }

    public function render()
    {
        $assetAccounts = ChartOfAccount::where('type', 'asset')->where('is_active', true)->orderBy('code')->get();
        $liabilityAccounts = ChartOfAccount::where('type', 'liability')->where('is_active', true)->orderBy('code')->get();
        $equityAccounts = ChartOfAccount::where('type', 'equity')->where('is_active', true)->orderBy('code')->get();

        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;
        $assets = [];
        $liabilities = [];
        $equities = [];

        foreach ($assetAccounts as $acc) {
            $debit = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                ->sum('debit');
            $credit = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                ->sum('credit');
            $balance = $debit - $credit;
            if ($balance != 0) {
                $assets[] = (object)['name' => $acc->name, 'balance' => $balance];
                $totalAssets += $balance;
            }
        }

        foreach ($liabilityAccounts as $acc) {
            $credit = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                ->sum('credit');
            $debit = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                ->sum('debit');
            $balance = $credit - $debit;
            if ($balance != 0) {
                $liabilities[] = (object)['name' => $acc->name, 'balance' => $balance];
                $totalLiabilities += $balance;
            }
        }

        foreach ($equityAccounts as $acc) {
            $credit = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                ->sum('credit');
            $debit = (float) JournalEntryItem::where('account_id', $acc->id)
                ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                ->sum('debit');
            $balance = $credit - $debit;
            if ($balance != 0) {
                $equities[] = (object)['name' => $acc->name, 'balance' => $balance];
                $totalEquity += $balance;
            }
        }

        return view('livewire.accounting.balance-sheet', compact(
            'assets', 'liabilities', 'equities',
            'totalAssets', 'totalLiabilities', 'totalEquity'
        ));
    }
}
