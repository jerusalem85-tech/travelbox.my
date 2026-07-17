<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.app')]
class GeneralLedger extends Component
{
    use WithPagination;

    #[Url]
    public string $account_id = '';

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
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();

        $query = JournalEntry::with(['items.account', 'trip'])->whereHas('items');

        if ($this->account_id) {
            $query->whereHas('items', fn($q) => $q->where('account_id', $this->account_id));
        }

        if ($this->from) $query->where('date', '>=', $this->from);
        if ($this->to) $query->where('date', '<=', $this->to);

        $entries = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(20);

        $selectedAccount = $this->account_id ? ChartOfAccount::find($this->account_id) : null;

        $totals = ['debit' => 0, 'credit' => 0];
        foreach ($entries as $e) {
            foreach ($e->items as $item) {
                $totals['debit'] += (float) $item->debit;
                $totals['credit'] += (float) $item->credit;
            }
        }

        return view('livewire.accounting.general-ledger', compact('accounts', 'entries', 'selectedAccount', 'totals'));
    }
}
