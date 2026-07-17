<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryItem;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class TrialBalance extends Component
{
    #[Url]
    public string $as_of = '';

    public function mount(): void
    {
        if (!$this->as_of) $this->as_of = now()->format('Y-m-d');
    }

    public function render()
    {
        $data = ChartOfAccount::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(function ($account) {
                $debit = (float) JournalEntryItem::where('account_id', $account->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                    ->sum('debit');
                $credit = (float) JournalEntryItem::where('account_id', $account->id)
                    ->whereHas('journalEntry', fn($q) => $q->where('date', '<=', $this->as_of))
                    ->sum('credit');

                return (object) [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $debit - $credit,
                ];
            });

        $totalDebit = $data->sum('debit');
        $totalCredit = $data->sum('credit');

        return view('livewire.accounting.trial-balance', compact('data', 'totalDebit', 'totalCredit'));
    }
}
