<div>
    <x-page-header title="General Ledger" description="All journal entries with account filters" />

    <x-card>
        <div class="row mb-3">
            <div class="col-md-4">
                <select wire:model.live="account_id" class="form-control">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="from" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="to" class="form-control">
            </div>
            <div class="col-md-2">
                <button wire:click="$refresh" class="btn btn-outline-primary w-100"><i class="fas fa-search"></i> Filter</button>
            </div>
        </div>

        @if($selectedAccount)
            <div class="alert alert-info">
                <strong>Account:</strong> {{ $selectedAccount->code }} - {{ $selectedAccount->name }}
                <span class="ms-3 badge bg-{{ $selectedAccount->type === 'asset' ? 'primary' : ($selectedAccount->type === 'income' ? 'success' : 'warning') }}">
                    {{ ucfirst($selectedAccount->type) }}
                </span>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Description</th>
                        <th>Trip</th>
                        <th>Account</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                        @foreach($entry->items as $item)
                            <tr>
                                <td>{{ $entry->date->format('d/m/Y') }}</td>
                                <td><code>{{ $entry->entry_number ?? '#' . $entry->id }}</code></td>
                                <td>{{ $entry->description }}</td>
                                <td>
                                    @if($entry->trip)
                                        <a href="{{ route('trips.show', $entry->trip) }}" class="text-decoration-none">{{ $entry->trip->trip_number }}</a>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $item->account?->code }}</small>
                                    <span class="ms-1">{{ $item->account?->name }}</span>
                                </td>
                                <td class="text-end text-danger">{{ $item->debit > 0 ? number_format($item->debit, 2) : '' }}</td>
                                <td class="text-end text-success">{{ $item->credit > 0 ? number_format($item->credit, 2) : '' }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No entries found</td></tr>
                    @endforelse
                </tbody>
                @if($totals['debit'] > 0 || $totals['credit'] > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="5" class="text-end">Totals</td>
                            <td class="text-end">{{ number_format($totals['debit'], 2) }}</td>
                            <td class="text-end">{{ number_format($totals['credit'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="mt-3">{{ $entries->links() }}</div>
    </x-card>
</div>
