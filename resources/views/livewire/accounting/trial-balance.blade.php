<div>
    <x-page-header title="Trial Balance" description="As of {{ \Carbon\Carbon::parse($as_of)->format('d/m/Y') }}" />

    <x-card>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" wire:model.live="as_of" class="form-control">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Account</th>
                        <th>Type</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td><code>{{ $item->code }}</code></td>
                            <td>{{ $item->name }}</td>
                            <td><span class="badge bg-secondary">{{ ucfirst($item->type) }}</span></td>
                            <td class="text-end text-danger">{{ $item->balance > 0 ? number_format($item->balance, 2) : '' }}</td>
                            <td class="text-end text-success">{{ $item->balance < 0 ? number_format(abs($item->balance), 2) : '' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No accounts found</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Totals</td>
                        <td class="text-end">{{ number_format($totalDebit, 2) }}</td>
                        <td class="text-end">{{ number_format($totalCredit, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>
</div>
