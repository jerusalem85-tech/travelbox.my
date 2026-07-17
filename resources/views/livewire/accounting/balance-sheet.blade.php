<div>
    <x-page-header title="Balance Sheet" description="As of {{ \Carbon\Carbon::parse($as_of)->format('d/m/Y') }}" />

    <x-card>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" wire:model.live="as_of" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="text-primary mb-3"><i class="fas fa-building"></i> Assets</h5>
                <table class="table table-sm">
                    <thead class="table-light"><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        @forelse($assets as $item)
                            <tr><td>{{ $item->name }}</td><td class="text-end">{{ number_format($item->balance, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-muted text-center">No assets</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td>Total Assets</td><td class="text-end">{{ number_format($totalAssets, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="text-warning mb-3"><i class="fas fa-credit-card"></i> Liabilities</h5>
                <table class="table table-sm">
                    <thead class="table-light"><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        @forelse($liabilities as $item)
                            <tr><td>{{ $item->name }}</td><td class="text-end">{{ number_format($item->balance, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-muted text-center">No liabilities</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td>Total Liabilities</td><td class="text-end">{{ number_format($totalLiabilities, 2) }}</td></tr>
                    </tfoot>
                </table>

                <h5 class="text-info mb-3 mt-3"><i class="fas fa-chart-pie"></i> Equity</h5>
                <table class="table table-sm">
                    <thead class="table-light"><tr><th>Account</th><th class="text-end">Balance</th></tr></thead>
                    <tbody>
                        @forelse($equities as $item)
                            <tr><td>{{ $item->name }}</td><td class="text-end">{{ number_format($item->balance, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="text-muted text-center">No equity</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr><td>Total Equity</td><td class="text-end">{{ number_format($totalEquity, 2) }}</td></tr>
                    </tfoot>
                </table>

                <div class="alert alert-info mt-3">
                    <strong>Liabilities + Equity:</strong> {{ number_format($totalLiabilities + $totalEquity, 2) }}
                </div>
            </div>
        </div>
    </x-card>
</div>
