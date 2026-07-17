<div>
    <x-page-header title="Profit & Loss Statement" description="{{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}" />

    <x-card>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" wire:model.live="from" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="to" class="form-control">
            </div>
        </div>

        <h5 class="text-success mb-3"><i class="fas fa-arrow-up"></i> Income</h5>
        <table class="table table-sm">
            <thead class="table-light">
                <tr><th>Account</th><th class="text-end">Amount</th></tr>
            </thead>
            <tbody>
                @forelse($incomeItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="text-end text-success">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted text-center">No income recorded</td></tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total Income</td>
                    <td class="text-end text-success">{{ number_format($totalIncome, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <h5 class="text-danger mb-3 mt-4"><i class="fas fa-arrow-down"></i> Expenses</h5>
        <table class="table table-sm">
            <thead class="table-light">
                <tr><th>Account</th><th class="text-end">Amount</th></tr>
            </thead>
            <tbody>
                @forelse($expenseItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="text-end text-danger">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted text-center">No expenses recorded</td></tr>
                @endforelse
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total Expenses</td>
                    <td class="text-end text-danger">{{ number_format($totalExpense, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="card {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }} text-white mt-4">
            <div class="card-body text-center py-3">
                <h4 class="mb-0">{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}: {{ number_format(abs($netProfit), 2) }}</h4>
            </div>
        </div>
    </x-card>
</div>
